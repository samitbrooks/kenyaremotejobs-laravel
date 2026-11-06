import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

const template = readFileSync(new URL('../resources/views/components/action-center.blade.php', import.meta.url), 'utf8');
const script = template.match(/<script data-kazi-assistant>([\s\S]*?)<\/script>/)?.[1];
assert.ok(script, 'The assistant component must provide its state factory.');

function assistant(fetchImplementation = async () => ({ ok: true, json: async () => ({ reply: 'A useful next step.' }) })) {
    const window = { location: { origin: 'https://kenyaremotejobs.test' } };
    runInNewContext(script, { window, URL, fetch: fetchImplementation });
    const state = window.kaziAssistant({ endpoint: '/api/career-bot', csrfToken: 'test-token' });
    state.$nextTick = callback => callback();
    state.$refs = { input: { focus() {} }, launcher: { focus() {} }, chatContainer: { scrollTop: 0, scrollHeight: 100 } };
    return state;
}

test('minimising and restoring preserves conversation and draft', () => {
    const state = assistant();
    state.open();
    state.messages.push({ role: 'user', content: 'Help with my CV' });
    state.inputMessage = 'My unfinished follow-up';
    const messages = state.messages;
    state.minimize();
    assert.equal(state.isOpen, true);
    assert.equal(state.isMinimized, true);
    state.open();
    assert.equal(state.isMinimized, false);
    assert.equal(state.messages, messages);
    assert.equal(state.inputMessage, 'My unfinished follow-up');
});

test('a reply received while minimised is kept without reopening the panel', async () => {
    let finish;
    const response = new Promise(resolve => { finish = resolve; });
    const state = assistant(() => response);
    state.open();
    state.inputMessage = 'What can I do next?';
    const sending = state.send();
    state.minimize();
    finish({ ok: true, json: async () => ({ reply: 'Start with a shortlist.' }) });
    await sending;
    assert.equal(state.isMinimized, true);
    assert.equal(state.messages.at(-1).content, 'Start with a shortlist.');
    assert.equal(state.loading, false);
});

test('history fits the public API limits and does not duplicate the current question', async () => {
    let payload;
    const state = assistant(async (_url, options) => {
        payload = JSON.parse(options.body);
        return { ok: true, json: async () => ({ reply: 'Next step.' }) };
    });
    for (let index = 0; index < 8; index++) {
        state.messages.push({ role: index % 2 ? 'assistant' : 'user', content: 'x'.repeat(900) });
    }
    state.inputMessage = 'My current question';
    await state.send();
    assert.equal(payload.history.length, 4);
    assert.ok(payload.history.every(message => message.content.length <= 500));
    assert.ok(payload.history.every(message => message.content !== payload.message));
    assert.equal(payload.message, 'My current question');
});

test('failed requests restore the question for retry', async () => {
    const state = assistant(async () => { throw new Error('Offline'); });
    state.inputMessage = 'Please help with my interview';
    await state.send();
    assert.equal(state.inputMessage, 'Please help with my interview');
    assert.equal(state.loading, false);
    assert.match(state.error, /try again/i);
});

test('rate limiting gives a retry message and does not ask for payment', async () => {
    const state = assistant(async () => ({ ok: false, status: 429 }));
    await state.send('Help me find work');
    assert.match(state.error, /wait a moment/i);
    assert.doesNotMatch(state.error, /pay|upgrade|membership/i);
    assert.equal(state.inputMessage, 'Help me find work');
});

test('duplicate sends while waiting do not create another request', async () => {
    let calls = 0;
    const state = assistant(async () => {
        calls++;
        return { ok: true, json: async () => ({ reply: 'Done.' }) };
    });
    await Promise.all([state.send('Question'), state.send('Question')]);
    assert.equal(calls, 1);
});

test('assistant output escapes HTML while supporting simple emphasis', () => {
    const formatted = assistant().formatMarkdown('<img src=x onerror=alert(1)> **Hello**');
    assert.ok(!formatted.includes('<img'));
    assert.ok(formatted.includes('&lt;img'));
    assert.ok(formatted.includes('<strong>Hello</strong>'));
});

test('suggested links are limited to this site', () => {
    const state = assistant();
    assert.equal(state.safeActionUrl('/jobs?onlineWork=true#roles'), '/jobs?onlineWork=true#roles');
    assert.equal(state.safeActionUrl('javascript:alert(1)'), null);
    assert.equal(state.safeActionUrl('//elsewhere.example/jobs'), null);
    assert.equal(state.safeActionUrl('https://elsewhere.example/jobs'), null);
});
