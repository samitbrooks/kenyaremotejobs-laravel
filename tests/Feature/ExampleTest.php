<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Pricing');

        // Unsubscribed guest is redirected to pricing (FlexJobs subscription model)
        $this->get('/jobs')->assertRedirect('/pricing');

        // Subscribed user can access jobs
        $subscriber = User::factory()->create(['subscribed' => true]);
        $this->actingAs($subscriber)->get('/jobs')->assertStatus(200);

        $this->get('/pricing')->assertStatus(200);
        $this->get('/companies')->assertStatus(200);
        $this->get('/collections')->assertStatus(200);
        $this->get('/faqs')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
    }
}
