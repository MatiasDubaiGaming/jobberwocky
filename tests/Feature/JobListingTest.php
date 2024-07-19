<?php
namespace Tests\Feature;

use App\Events\JobCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Mockery;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close();
    }

    /**
     *  console: sail  artisan test --filter testJobCreatedEventIsDispatched
     *  @group excludeFromDefaultRun
     */
    public function test_job_created_event_is_dispatched()
    {
        Event::fake();

        $jobData = [
            'title' => 'Software Developer',
            'description' => 'Develop and maintain web applications.',
            'company' => 'Tech Company',
            'skills' => 'PHP, Laravel, JavaScript',
            'location' => 'Remote',
            'salary' => 70000,
        ];

        $response = $this->postJson('/api/job_listings', $jobData);

        dump($response->getContent());
        $response->assertStatus(201);

        Event::assertDispatched( JobCreated::class, function ($event) use ($jobData) {
            return $event->jobListing->title === $jobData['title'] &&
                $event->jobListing->description === $jobData['description'];
        });
    }

}
