<?php

namespace App\Services;

// Upstream job descriptions routinely name-drop the employer or the board
// they were posted to in the body text itself ("Bright Vision Technologies
// is a...", "Originally posted on Himalayas") — hiding the `company` field
// on a locked job does nothing if that text still says who's hiring. This
// scrubs every surface a locked job's text reaches (card preview, full
// description, meta tags, JSON-LD) before it's ever rendered. Deliberately
// aggressive: a false-positive redaction costs a few odd words, a missed
// one costs the whole paywall — bias hard toward the former.
//
// Ported 1:1 from the Next.js version's src/lib/redact.ts, including every
// fix already found the hard way against real leaking listings (the
// KIRA/Bjak self-declared-name case, the Moniepoint/TeamApt alias case).
class Redactor
{
    private const KNOWN_SOURCE_NAMES = ['Himalayas', 'Remote OK', 'RemoteOK', 'Remotive', 'Arbeitnow', 'Jobicy'];

    // Corporate-suffix words are too generic to redact on their own — doing
    // so would nuke unrelated text in every *other* listing ("we use modern
    // technologies", "a leading solutions provider"). Only a company's
    // distinctive words get the word-level pass below.
    private const GENERIC_COMPANY_WORDS = [
        'inc', 'llc', 'ltd', 'limited', 'corp', 'corporation', 'company', 'co',
        'group', 'technologies', 'technology', 'tech', 'solutions', 'solution',
        'consulting', 'services', 'systems', 'software', 'labs', 'studio',
        'studios', 'global', 'international', 'holdings', 'partners', 'plc',
        'gmbh', 'the', 'and', 'of',
    ];

    // Words that can open a sentence capitalized without being a company
    // name — "About Us" / "About This Role" are near-universal job-posting
    // headings, and "We are…" / "It is…" are common sentence starters, not
    // an employer's name. Shared by both extractSelfDeclaredName() patterns.
    private const NAME_STOPWORDS = [
        'us', 'the', 'this', 'our', 'me', 'you', 'we', 'role', 'position', 'job',
        'company', 'team', 'opportunity', 'it', 'they', 'i', 'here',
    ];

    public function redactEmployerIdentity(string $text, string $company): string
    {
        if ($text === '') {
            return $text;
        }

        $result = $text;

        // URLs and emails can point straight back to the employer's site or
        // a direct contact — strip before anything else.
        $result = preg_replace('#https?://\S+#i', '[link removed]', $result);
        $result = preg_replace('#\bwww\.\S+#i', '[link removed]', $result);
        $result = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[email removed]', $result);

        // Pulled from the *original* text, before any redaction runs on it —
        // by the time "About KIRA" itself got redacted below it'd be too
        // late to notice KIRA was the name to strip everywhere else it's
        // mentioned.
        $selfDeclaredName = $this->extractSelfDeclaredName($text);

        $namesToStrip = array_filter(array_map(
            fn ($name) => trim($name),
            array_filter([$company, $selfDeclaredName, ...self::KNOWN_SOURCE_NAMES])
        ));

        foreach ($namesToStrip as $name) {
            $result = preg_replace('/\b'.preg_quote($name, '/').'\b/i', '[Employer]', $result);
        }

        $wordsToStrip = array_merge(
            $this->significantWords($company),
            $selfDeclaredName ? $this->significantWords($selfDeclaredName) : []
        );
        foreach ($wordsToStrip as $word) {
            $result = preg_replace('/\b'.preg_quote($word, '/').'\b/i', '[Employer]', $result);
        }

        return $result;
    }

    /**
     * @param  array<int, string>  $tags
     * @return array<int, string>
     */
    public function redactTags(array $tags, string $company): array
    {
        $blocked = array_map(
            fn ($name) => strtolower(trim($name)),
            array_merge([$company], self::KNOWN_SOURCE_NAMES, $this->significantWords($company))
        );
        $blocked = array_flip($blocked);

        return array_values(array_filter(
            $tags,
            fn ($tag) => ! isset($blocked[strtolower(trim($tag))])
        ));
    }

    // Descriptions frequently open with "About {Name}" as a section
    // heading, where {Name} is a product or parent-company brand that has
    // no textual relationship to the registered company field at all — e.g.
    // a listing for employer "Bjak" opening "About KIRA" (KIRA being Bjak's
    // product brand). No amount of splitting the *registered* name into
    // words can catch an alias that shares none of its letters, so this
    // pulls the name the description gives for itself directly out of that
    // heading instead. Falls back to the even more common "{Name} is a
    // mission-driven organization…" boilerplate company-bio opener when
    // there's no "About" heading at all — e.g. a listing posted through a
    // staffing agency, where the registered `company` field is the agency's
    // name and the description's bio paragraph names the actual employer.
    private function extractSelfDeclaredName(string $text): ?string
    {
        // The continuation between words is deliberately [ \t]+, not \s+ —
        // an earlier version used \s+ throughout and let the capture bleed
        // across a paragraph break into the *next* sentence ("About
        // KIRA\n\nOur mission…" captured "KIRA  Our"), which then redacted
        // every occurrence of the word "our" in the whole description. A
        // period has the same effect for a same-line sentence end ("About
        // Cerianna. Be part of…" captured "Cerianna. Be") — dropping "."
        // from the word-character class means a sentence-ending period
        // simply isn't a character the match can continue through, so it
        // stops the capture there too, on either line endings or sentence
        // endings.
        if (preg_match("/\b[Aa]bout[ \t]+([A-Z][\w&'-]*(?:[ \t]+[A-Z][\w&'-]*){0,3})/", $text, $matches)) {
            $candidate = $this->cleanSelfDeclaredCandidate($matches[1]);
            if ($candidate !== null) {
                return $candidate;
            }
        }

        // Anchored to the start of a sentence (string start, a newline, or
        // ". ") so it can't match a capitalized proper noun mentioned
        // mid-sentence elsewhere — only an actual sentence opening like
        // "Veeva Systems is a mission-driven organization…" or "Acme Corp
        // is the leading provider of…".
        if (preg_match("/(?:^|\\n|\\.[ \\t]+)([A-Z][\\w&'-]*(?:[ \\t]+[A-Z][\\w&'-]*){0,3})[ \\t]+(?:is|are)[ \\t]+(?:a|an|the)\\b/", $text, $matches)) {
            return $this->cleanSelfDeclaredCandidate($matches[1]);
        }

        return null;
    }

    private function cleanSelfDeclaredCandidate(string $raw): ?string
    {
        $candidate = preg_replace('/[.,:;!?]+$/', '', trim($raw));
        $firstWord = strtolower(explode(' ', trim($candidate))[0] ?? '');

        if ($firstWord === '' || in_array($firstWord, self::NAME_STOPWORDS, true)) {
            return null;
        }

        return $candidate;
    }

    // A description will sometimes call the employer by an internal
    // abbreviation or nickname instead of its registered name ("BV Teck"
    // for "Bright Vision Technologies") — no string match catches an alias
    // with no textual relationship to the original. Redacting each
    // distinctive word of the company name individually (not just the full
    // phrase) narrows that gap without needing an AI pass to actually
    // understand the text.
    /**
     * @return array<int, string>
     */
    private function significantWords(string $company): array
    {
        $words = preg_split('/\s+/', $company);

        return array_values(array_filter(array_map(
            fn ($word) => preg_replace('/[^\p{L}\p{N}]/u', '', $word),
            $words
        ), fn ($word) => mb_strlen($word) > 2 && ! in_array(mb_strtolower($word), self::GENERIC_COMPANY_WORDS, true)));
    }
}
