<?php

namespace Tests\Feature;

use App\Mail\MarketingEmail;
use App\Models\JobListing;
use App\Models\User;
use App\Services\BulkMailer;
use App\Services\PaymentService;
use Database\Seeders\SeoPillarContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SeoEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_contains_canonical_and_open_graph_tags(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('name="twitter:card"', false);
        $response->assertSee('KenyaRemoteJobs', false);
    }

    public function test_sitemap_xml_contains_static_category_and_journal_urls(): void
    {
        $this->seed(SeoPillarContentSeeder::class);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<loc>', false);
        $response->assertSee('/remote-jobs/customer-support-kenya</loc>', false);
        $response->assertSee('/remote-jobs/software-developer-kenya</loc>', false);
        $response->assertSee('/remote-jobs/virtual-assistant-kenya</loc>', false);
        $response->assertSee('/journal/complete-guide-to-legit-remote-jobs-in-kenya</loc>', false);
        $response->assertSee('/journal/aeo-geo-aio-guide-ai-search-optimization</loc>', false);
        $response->assertSee('/journal/generative-engine-optimization-geo-playbook</loc>', false);
    }

    public function test_robots_txt_points_to_sitemap_and_disallows_admin(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Disallow: /account');
        $response->assertSee('Sitemap:');
        $response->assertSee('sitemap.xml');
        $response->assertSee('llms.txt');
        $response->assertSee('llms-full.txt');
    }

    public function test_home_page_contains_llms_txt_discovery_tag(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<link rel="alternate" type="text/markdown" title="LLMs.txt"', false);
    }

    public function test_llms_txt_returns_curated_markdown_documentation(): void
    {
        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('# KenyaRemoteJobs', false);
        $response->assertSee('## Core Platform & Tools', false);
        $response->assertSee('## Remote Roles by Discipline', false);
        $response->assertSee('## Machine-Readable Feeds & Endpoints', false);
        $response->assertSee('/llms-full.txt', false);

        $wellKnownResponse = $this->get('/.well-known/llms.txt');
        $wellKnownResponse->assertStatus(200);
        $wellKnownResponse->assertSee('# KenyaRemoteJobs', false);
    }

    public function test_llms_full_txt_returns_complete_kenyan_contracting_context_and_jobs(): void
    {
        JobListing::create([
            'id' => 'test-llm-job',
            'source_name' => 'employer',
            'source_id' => 'test-llm-1',
            'source_url' => 'https://example.com/jobs/1',
            'tier' => 'standard',
            'title' => 'Senior Remote Laravel Engineer',
            'company' => 'Acme Global',
            'description' => 'Test job description',
            'tags' => ['Laravel', 'PHP'],
            'origin' => 'employer',
            'remote_type' => 'Full Remote',
            'location' => 'Worldwide',
            'salary' => '$90,000 - $120,000 USD/yr',
            'kenya_friendly' => true,
            'kenya_score' => 98,
            'kenya_reasons' => ['Worldwide remote'],
            'audience_segments' => [],
            'posted_at' => now(),
        ]);

        $response = $this->get('/llms-full.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
        $response->assertSee('# KenyaRemoteJobs — Full System & Knowledge Context', false);
        $response->assertSee('## 1. Remote Contracting Context for Kenya', false);
        $response->assertSee('Timezone Advantage: Nairobi is on East Africa Time (EAT / UTC+3)', false);
        $response->assertSee('IRS Form W-8BEN', false);
        $response->assertSee('KRA PIN', false);
        $response->assertSee('Payout Methods', false);
        $response->assertSee('Wise', false);
        $response->assertSee('## 2. Active Verified Kenya-Friendly Roles', false);
        $response->assertSee('Senior Remote Laravel Engineer at Acme Global', false);
        $response->assertSee('## 3. Remote Companies Vetted for Kenya', false);
        $response->assertSee('GitLab', false);
        $response->assertSee('Automattic', false);
    }

    public function test_programmatic_category_landing_page_renders_with_seo_and_schemas(): void
    {
        $response = $this->get('/remote-jobs/customer-support-kenya');

        $response->assertStatus(200);
        $response->assertSee('Remote Customer Support');
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('/remote-jobs/customer-support-kenya', false);
        $response->assertSee('FAQPage', false);
        $response->assertSee('ItemList', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Explore Other Remote Roles in Kenya');
    }

    public function test_programmatic_category_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/remote-jobs/non-existent-category-slug');

        $response->assertStatus(404);
    }

    public function test_journal_article_renders_markdown_and_article_schema(): void
    {
        $this->seed(SeoPillarContentSeeder::class);

        $response = $this->get('/journal/complete-guide-to-legit-remote-jobs-in-kenya');

        $response->assertStatus(200);
        $response->assertSee('Article', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('<h2', false);
        $response->assertSee('images.unsplash.com', false);
        $response->assertSee('property="og:image"', false);
        $response->assertSee('Browse Open Remote Jobs');
    }

    public function test_journal_index_renders_all_seeded_articles(): void
    {
        $this->seed(SeoPillarContentSeeder::class);

        $response = $this->get('/journal');

        $response->assertStatus(200);
        $response->assertSee('Ideas for better work');
        $response->assertDontSee('Nothing published yet');
        $response->assertSee('images.unsplash.com', false);
        $response->assertSee('How to Become a High-Earning Virtual Assistant in Kenya');
        $response->assertSee('KRA Tax Guide for Remote Workers & Freelancers in Kenya');
        $response->assertSee('How to Land Remote Customer Support & Success Jobs from Kenya');
        $response->assertSee('The Essential Remote Work Setup in Kenya');
        $response->assertSee('7 Warning Signs of Online Job Scams in Kenya');
    }

    public function test_job_detail_page_has_canonical_and_job_posting_schema(): void
    {
        $job = JobListing::create([
            'id' => 'test-job-seo-123',
            'source_name' => 'employer',
            'source_id' => 'test-123',
            'source_url' => 'https://kenyaremotejobs.com',
            'tier' => 'featured',
            'title' => 'Senior Remote Engineer',
            'company' => 'Acme Global',
            'description' => 'Great remote engineering role.',
            'tags' => ['Engineer', 'PHP'],
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'origin' => 'employer',
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'audience_segments' => [],
            'posted_at' => now(),
        ]);

        $response = $this->withHeaders(['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)'])->get('/jobs/'.$job->id);

        $response->assertStatus(200);
        $response->assertSee('JobPosting', false);
        $response->assertSee('directApply', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('/jobs/'.$job->id, false);
    }

    public function test_faqs_page_renders_with_faq_page_schema(): void
    {
        $response = $this->get('/faqs');

        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('FAQPage', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('/faqs', false);
        $response->assertSee('What is KenyaRemoteJobs?');
        $response->assertSee('Kenya-Friendly Match');
    }

    public function test_faq_redirects_to_faqs(): void
    {
        $response = $this->get('/faq');

        $response->assertRedirect('/faqs');
    }

    public function test_bulk_mailer_test_email_dispatch(): void
    {
        Mail::fake();

        $mailer = app(BulkMailer::class);
        $result = $mailer->sendTest('admin@example.com', 'Test Subject', 'Test Body Message');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('successfully', $result['message']);
        Mail::assertSent(MarketingEmail::class);
    }

    public function test_faq_reframed_and_does_not_list_job_sources(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('How are job listings sourced and verified?');
        $homeResponse->assertDontSee('Arbeitnow');
        $homeResponse->assertDontSee('RemoteOK');
        $homeResponse->assertDontSee('Jobicy');
        $homeResponse->assertDontSee('Himalayas');

        $faqResponse = $this->get('/faqs');
        $faqResponse->assertStatus(200);
        $faqResponse->assertSee('How are job listings sourced and verified?');
        $faqResponse->assertDontSee('Arbeitnow');
        $faqResponse->assertDontSee('RemoteOK');
        $faqResponse->assertDontSee('Jobicy');
        $faqResponse->assertDontSee('Himalayas');
    }

    public function test_companies_directory_index_renders_with_verified_companies(): void
    {
        $response = $this->get('/companies');

        $response->assertStatus(200);
        $response->assertSee('Top Global Companies Hiring Remotely in Kenya');
        $response->assertSee('Automattic');
        $response->assertSee('GitLab');
        $response->assertSee('Canonical');
        $response->assertSee('Superside');
        $response->assertSee('Deel');
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('/companies', false);
    }

    public function test_company_profile_renders_details_perks_and_schemas(): void
    {
        $response = $this->get('/companies/automattic');

        $response->assertStatus(200);
        $response->assertSee('Automattic');
        $response->assertSee('Why Automattic Hires in Kenya');
        $response->assertSee('B2B Independent Contractor or Global EOR');
        $response->assertSee('Organization', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('Key Perks &amp; Benefits', false);
    }

    public function test_company_profile_returns_404_for_invalid_company(): void
    {
        $response = $this->get('/companies/non-existent-company');

        $response->assertStatus(404);
    }

    public function test_collections_index_renders_all_curated_collections(): void
    {
        $response = $this->get('/collections');

        $response->assertStatus(200);
        $response->assertSee('High-Intent Remote Job Collections for Kenya');
        $response->assertSee('Remote Jobs Paying via Wise &amp; M-Pesa in Kenya', false);
        $response->assertSee('Worldwide Remote Jobs with Zero Visa Restrictions');
        $response->assertSee('Entry-Level Remote Jobs Open to Kenya');
        $response->assertSee('High-Paying Remote Jobs in Kenya');
        $response->assertSee('EAT-Friendly Remote Jobs in Kenya');
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('/collections', false);
    }

    public function test_collection_show_renders_jobs_faqs_and_schemas(): void
    {
        JobListing::create([
            'id' => 'col-test-job-1',
            'source_name' => 'employer',
            'source_id' => 'col-1',
            'source_url' => 'https://kenyaremotejobs.com',
            'tier' => 'featured',
            'title' => 'Junior Support Specialist',
            'company' => 'Global Corp',
            'description' => 'Entry level customer support role.',
            'tags' => ['Support', 'Customer Service'],
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'salary' => '$2,000/mo',
            'annual_salary_usd' => ['min' => 24000, 'max' => 30000],
            'origin' => 'employer',
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'audience_segments' => [],
            'posted_at' => now(),
        ]);

        $response = $this->get('/collections/companies-paying-via-wise-mpesa-kenya');

        $response->assertStatus(200);
        $response->assertSee('Remote Jobs with Direct Wise, Bank Wire & M-Pesa Payouts');
        $response->assertSee('FAQPage', false);
        $response->assertSee('ItemList', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('Explore Other Curated Collections');
    }

    public function test_collection_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/collections/non-existent-collection');

        $response->assertStatus(404);
    }

    public function test_kes_salary_estimate_badge_appears_on_job_card(): void
    {
        $job = JobListing::create([
            'id' => 'kes-test-job-1',
            'source_name' => 'employer',
            'source_id' => 'kes-1',
            'source_url' => 'https://kenyaremotejobs.com',
            'tier' => 'featured',
            'title' => 'Lead Software Architect',
            'company' => 'Tech Corp',
            'description' => 'High salary engineering job.',
            'tags' => ['Engineer', 'PHP'],
            'location' => 'Worldwide',
            'remote_type' => 'Full-time',
            'salary' => '$80,000 - $100,000 / year',
            'annual_salary_usd' => ['min' => 84000, 'max' => 120000],
            'origin' => 'employer',
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Global remote'],
            'audience_segments' => [],
            'posted_at' => now(),
        ]);

        $response = $this->withHeaders(['User-Agent' => 'Googlebot'])->get('/jobs/'.$job->id);

        $response->assertStatus(200);
        $response->assertSee('~KES 910,000 - 1,300,000/mo', false);
    }

    public function test_employers_page_renders_kenya_value_propositions_and_timezone_matrix(): void
    {
        $response = $this->get('/employers');

        $response->assertStatus(200);
        $response->assertSee('Hire world-class remote talent in');
        $response->assertSee('Top-Tier English Fluency');
        $response->assertSee('East Africa Time (UTC+3) Timezone Matrix');
        $response->assertSee('London (GMT / BST)');
        $response->assertSee('Berlin / Paris (CET)');
        $response->assertSee('New York (EST / EDT)');
        $response->assertSee('Simple, Transparent Job Posting Plans');
        $response->assertSee('/employers/post', false);
    }

    public function test_user_can_sign_up_and_be_instantly_authenticated(): void
    {
        $response = $this->post('/account/login', [
            'name' => 'Samit Brooks',
            'email' => 'samitbrooks@gmail.com',
            'redirectTo' => '/jobs/himalayas--professional-services-engineer',
        ]);

        $response->assertRedirect('/jobs/himalayas--professional-services-engineer');
        $this->assertAuthenticated();

        $user = User::where('email', 'samitbrooks@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Samit Brooks', $user->name);
    }

    public function test_existing_user_can_log_in_instantly(): void
    {
        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'secret',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/account/login', [
            'email' => 'existing@example.com',
            'redirectTo' => '/account',
        ]);

        $response->assertRedirect('/account');
        $this->assertAuthenticatedAs($user);
    }

    public function test_surveys_page_renders_with_item_list_and_faq_schemas(): void
    {
        $response = $this->get('/surveys');

        $response->assertStatus(200);
        $response->assertSee('Earn While You Search');
        $response->assertSee('ySense');
        $response->assertSee('Freecash');
        $response->assertSee('"@type":"ItemList"', false);
        $response->assertSee('"@type":"FAQPage"', false);
        $response->assertSee('How do I withdraw survey earnings to M-Pesa in Kenya?');
    }

    public function test_surveys_page_filters_by_payout_and_category(): void
    {
        $mpesaResponse = $this->get('/surveys?payout=mpesa');
        $mpesaResponse->assertStatus(200);
        $mpesaResponse->assertSee('ySense');

        $researchResponse = $this->get('/surveys?category=research');
        $researchResponse->assertStatus(200);
        $researchResponse->assertSee('Respondent.io');
    }

    public function test_sitemap_xml_contains_daily_surveys_entry(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee('/surveys</loc>', false);
        $response->assertSee('<changefreq>daily</changefreq>', false);
    }

    public function test_guest_sees_free_previews_and_kes_199_unlock_button(): void
    {
        $response = $this->get('/surveys');

        $response->assertStatus(200);
        $response->assertSee('Free Preview');
        $response->assertSee('KES 199');
        $response->assertSee('Unlock Premium Paid Surveys (KES 199)');
        $response->assertSee('Sign In to Unlock All Surveys with M-Pesa');
    }

    public function test_unlocked_user_sees_all_survey_platforms_without_locks(): void
    {
        $user = User::create([
            'name' => 'Paid User',
            'email' => 'paid@example.com',
            'password' => 'secret',
            'survey_pass_purchased_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/surveys');

        $response->assertStatus(200);
        $response->assertSee('VIP Vault Unlocked');
        $response->assertSee('Sign Up on Respondent.io');
        $response->assertSee('Sign Up on Outlier AI (Remotasks)');
        $response->assertDontSee('Unlock Vault (KES 199)');
    }

    public function test_purchase_survey_pass_fulfills_and_grants_access(): void
    {
        $user = User::create([
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => 'secret',
        ]);

        $this->assertFalse($user->hasSurveyAccess());

        $paymentService = app(PaymentService::class);
        $payment = $paymentService->purchaseSurveyPass($user, '0712345678');

        $this->assertEquals(199, $payment->amount_kes);
        $this->assertEquals('survey_pass', $payment->purpose);

        $user->refresh();
        $this->assertTrue($user->hasSurveyAccess());
    }

    public function test_seo_gsc_sitemap_command_fails_gracefully_when_unconfigured(): void
    {
        config([
            'services.google_indexing.key_path' => '/nonexistent/path.json',
            'services.google_indexing.credentials_json' => null,
        ]);

        $this->artisan('seo:gsc-sitemap submit')
            ->expectsOutputToContain('Google Search Console Property:')
            ->assertExitCode(1);
    }

    public function test_company_show_page_masks_company_identity_on_job_cards_for_guests(): void
    {
        JobListing::create([
            'id' => 'test-gitlab-sre',
            'origin' => 'synced',
            'tier' => 'basic',
            'title' => 'Senior Site Reliability Engineer',
            'company' => 'GitLab',
            'location' => 'Worldwide',
            'remote_type' => 'Full Remote',
            'description' => 'Help maintain global cloud infrastructure.',
            'source_id' => 'gl-1',
            'source_name' => 'GitLab Careers',
            'source_url' => 'https://gitlab.com/careers/apply-now',
            'posted_at' => now()->subDay(),
            'kenya_friendly' => true,
            'kenya_score' => 95,
            'kenya_reasons' => ['Worldwide remote'],
            'tags' => ['DevOps', 'Kubernetes'],
            'audience_segments' => [],
        ]);

        $response = $this->get('/companies/gitlab');

        $response->assertStatus(200);
        $response->assertSee('Verified Employer');
        $response->assertSee('Pro Only');
        $response->assertSee('View &amp; Unlock', false);
    }
}
