<?php

namespace App\Services\JobSources;

class CuratedStudentJobsSource implements JobSource
{
    /**
     * Retired: general platform sign-up pages are not current vacancies.
     * Live employer feeds supply student and online-work opportunities instead.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetch(): array
    {
        return [];
    }
}
