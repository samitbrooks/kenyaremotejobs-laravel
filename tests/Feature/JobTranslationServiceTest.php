<?php

namespace Tests\Feature;

use App\Services\Translator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class JobTranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_translator_translates_and_caches_result(): void
    {
        Cache::flush();

        Http::fake([
            'https://translate.googleapis.com/*' => Http::response([
                [
                    ['We are looking for a remote developer.', 'Nous recherchons un développeur à distance.'],
                ],
            ], 200),
        ]);

        $translator = app(Translator::class);
        $result = $translator->translate('Nous recherchons un développeur à distance.');

        $this->assertEquals('We are looking for a remote developer.', $result);

        // Verify result is cached in Cache
        $cacheKey = 'job_trans_en_'.md5('Nous recherchons un développeur à distance.');
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals('We are looking for a remote developer.', Cache::get($cacheKey));
    }

    public function test_translator_falls_back_to_mymemory_when_google_fails(): void
    {
        Cache::flush();

        Http::fake([
            'https://translate.googleapis.com/*' => Http::response(['error' => 'Too many requests'], 429),
            'https://api.mymemory.translated.net/*' => Http::response([
                'responseData' => [
                    'translatedText' => 'Hello from MyMemory fallback',
                ],
            ], 200),
        ]);

        $translator = app(Translator::class);
        $result = $translator->translate('Bonjour du fallback MyMemory');

        $this->assertEquals('Hello from MyMemory fallback', $result);
    }

    public function test_livewire_translate_toggle_component_renders_and_toggles(): void
    {
        Cache::flush();

        Http::fake([
            'https://translate.googleapis.com/*' => Http::response([
                [
                    ['English version of job posting.', 'Version française de l\'annonce.'],
                ],
            ], 200),
        ]);

        Livewire::test('translate-toggle', [
            'text' => 'Version française de l\'annonce.',
        ])
            ->assertSee('Translate to English')
            ->call('toggle')
            ->assertSee('Show original')
            ->assertSee('English version of job posting.')
            ->call('toggle')
            ->assertSee('Translate to English');
    }
}
