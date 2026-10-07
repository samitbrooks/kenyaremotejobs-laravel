<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalDomainTest extends TestCase
{
    use RefreshDatabase;

    public function test_www_subdomain_is_permanently_redirected_to_apex_domain(): void
    {
        $response = $this->get('http://www.kenyaremotejobs.com/jobs?q=Writing');

        $response->assertStatus(301);
        $this->assertStringContainsString('kenyaremotejobs.com/jobs?q=Writing', (string) $response->headers->get('Location'));
        $this->assertStringNotContainsString('www.kenyaremotejobs.com', (string) $response->headers->get('Location'));
    }

    public function test_canonical_tag_resolves_without_www_prefix(): void
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical" href="http://localhost:8000/jobs">', false);
    }
}
