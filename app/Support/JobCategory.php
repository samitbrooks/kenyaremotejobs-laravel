<?php

namespace App\Support;

class JobCategory
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        $labels = array_map(fn (array $category): string => $category['name'], self::definitions());
        asort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return [...$labels, 'uncategorised' => 'Uncategorised'];
    }

    /**
     * Assign one primary work category, preferring specific title phrases to tags.
     *
     * @param  array<int, string>  $tags
     */
    public static function classify(string $title, array $tags = []): string
    {
        $title = self::normalise($title);
        $tags = self::normalise(implode(' ', $tags));
        $bestCategory = 'uncategorised';
        $bestScore = 0;
        foreach (self::definitions() as $slug => $category) {
            foreach ($category['terms'] as $term) {
                $term = self::normalise($term);
                $pattern = '/\b'.preg_quote($term, '/').'\b/u';
                $score = preg_match($pattern, $title) ? 1000 + mb_strlen($term)
                    : (preg_match($pattern, $tags) ? mb_strlen($term) : 0);
                if ($score > $bestScore) {
                    $bestCategory = $slug;
                    $bestScore = $score;
                }
            }
        }

        return $bestCategory;
    }

    private static function normalise(string $value): string
    {
        return trim((string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower(strip_tags($value))));
    }

    /**
     * @return array<string, array{name: string, terms: array<int, string>}>
     */
    private static function definitions(): array
    {
        static $definitions = null;
        if ($definitions !== null) {
            return $definitions;
        }
        $aliases = [
            'customer-support-kenya' => ['customer experience', 'helpdesk', 'help desk'],
            'virtual-assistant-kenya' => ['executive assistant', 'personal assistant'],
            'data-entry-kenya' => ['data entry', 'data processing', 'operations assistant', 'administrative assistant'],
            'ai-training-annotation-kenya' => ['annotator', 'data labeler', 'data labeller', 'model evaluator', 'ai trainer'],
            'transcription-translation-kenya' => ['transcriptionist', 'captioner', 'subtitler'],
        ];
        $definitions = [];
        foreach (JobCategorySeo::all() as $slug => $category) {
            if (in_array($slug, ['entry-level-kenya', 'online-jobs-for-students-kenya', 'student-jobs-kenya'], true)) {
                continue;
            }
            $terms = $category['search_terms'];
            if ($slug === 'data-entry-kenya') {
                $terms = array_diff($terms, ['annotation', 'transcription']);
            }
            $definitions[$slug] = [
                'name' => $category['name'],
                'terms' => array_values(array_unique([...$terms, ...($aliases[$slug] ?? [])])),
            ];
        }

        return $definitions;
    }
}
