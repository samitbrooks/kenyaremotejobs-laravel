<?php

namespace App\Services\JobSources;

interface JobSource
{
    /**
     * @return array<int, array<string, mixed>> normalized job rows, keyed
     *                                          by the JobListing column names this sync pipeline writes.
     */
    public function fetch(): array;
}
