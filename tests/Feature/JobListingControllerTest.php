<?php
namespace Tests\Feature;

use App\Models\JobListing;
use App\Services\JobberwockyExtraSourceService;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class JobListingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /*
     * console: sail artisan test --filter testGetJobListings
     */
    public function testGetJobListings()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('query')->andReturnSelf();
        $jobListingMock->shouldReceive('get')->andReturn(collect([
            (object) ['title' => 'Software Engineer', 'location' => 'Spain']
        ]));

        $response = $this->get('/api/job_listings');

        $response->assertStatus(ResponseAlias::HTTP_OK);
    }

    /*
     * console: sail artisan test --filter testGetJobListingsWithParams
     */
    public function testGetJobListingsWithParams()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('query')->andReturnSelf();
        $jobListingMock->shouldReceive('where')
            ->with('title', 'like', '%Engineer%')
            ->andReturnSelf();
        $jobListingMock->shouldReceive('where')
            ->with('location', 'like', '%Spain%')
            ->andReturnSelf();
        $jobListingMock->shouldReceive('get')->andReturn(collect([
            (object) ['title' => 'Software Engineer', 'location' => 'Spain']
        ]));

        $response = $this->get('/api/job_listings?title=Engineer&location=Spain');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment(['title' => 'Software Engineer']);
    }

    /*
     * console: sail artisan test --filter testCreateJobListing
     */
    public function testCreateJobListing()
    {
        $jobListingMock = Mockery::mock('overload:App\Models\JobListing');

        $faker = Factory::create();

        $jobData = [
            'title' => $faker->jobTitle,
            'description' => $faker->text,
            'company' => $faker->company,
            'skills' => $faker->words(3, true),
            'location' => $faker->city,
            'salary' => $faker->numberBetween(30000, 100000),
        ];

        $jobListingMock->shouldReceive('create')
            ->with($jobData)
            ->andReturn(new JobListing($jobData));

        $response = $this->postJson('/api/job_listings', $jobData);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /*
     * console: sail artisan test --filter testUpdateJobListing
     */
    public function testUpdateJobListing()
    {
        $id = 1;
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');
        $jobListingMock->shouldReceive('findOrFail')->with(1)->andReturnSelf();

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'company' => 'Updated Company',
            'skills' => 'Updated Skills',
            'location' => 'Updated Location',
            'salary' => 90000,
        ];

        $jobListingMock->shouldReceive('update')->andReturn(true);
        $jobListingMock->shouldReceive('toJson')->andReturn($data);

        $response = $this->put('/api/job_listings/' . $id , $data);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment($data);
    }


    /*
     * console: sail artisan test --filter testUpdateJobListingNotFound
     */
    public function testUpdateJobListingNotFound()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('findOrFail')
            ->with(999)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException);

        $response = $this->put('/api/job_listings/999', [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /*
     * console: sail artisan test --filter testShowJobListing
     */
    public function testShowJobListing()
    {
        $id = 1;
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');
        $jobListing = [
            'id' => $id,
            'title' => 'Software Engineer',
            'description' => 'Job description',
            'company' => 'Company Name',
            'skills' => 'Skills required',
            'location' => 'Location',
            'salary' => 70000,
        ];

        $jobListingMock->shouldReceive('findOrFail')
            ->with($id)
            ->andReturnSelf();
        $jobListingMock->shouldReceive('toJson')
            ->andReturn($jobListing);


        $response = $this->get('/api/job_listings/' . $id);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment($jobListing);
    }

    /*
     * console: sail artisan test --filter testShowJobListingNotFound
     */
    public function testShowJobListingNotFound()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('findOrFail')
            ->with(999)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException);

        $response = $this->get('/api/job_listings/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /*
     * console: sail artisan test --filter testDestroyJobListing
     */
    public function testDestroyJobListing()
    {
        $id = 1;
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('destroyOrFail')
            ->with($id)
            ->andReturnSelf();

        $response = $this->delete('/api/job_listings/' . $id);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /*
     * console: sail artisan test --filter testDestroyJobListingNotFound
     */
    public function testDestroyJobListingNotFound()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');

        $jobListingMock->shouldReceive('findOrFail')
            ->with(999)
            ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException);

        $response = $this->delete('/api/job_listings/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /*
     * console: sail artisan test --filter testCombinedJobs
     */
    public function testCombinedJobs()
    {
        $jobListingMock = Mockery::mock('alias:App\Models\JobListing');
        $jobListingMock->shouldReceive('query')->andReturnSelf();
        $jobListingMock->shouldReceive('where')->andReturnSelf();
        $jobListingMock->shouldReceive('get')->andReturn(collect([
            (object) ['title' => 'Software Engineer', 'location' => 'Spain']
        ]));

        $extraSourceServiceMock = Mockery::mock(JobberwockyExtraSourceService::class);
        $extraSourceServiceMock->shouldReceive('fetchJobs')->andReturn([
            ['title' => 'Data Scientist', 'location' => 'USA']
        ]);

        $this->app->instance(JobberwockyExtraSourceService::class, $extraSourceServiceMock);

        $response = $this->get('/api/job_listings/combined_jobs?title=Engineer&location=Spain',['accept' => 'application/json']);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment(['title' => 'Software Engineer']);
        $response->assertJsonFragment(['title' => 'Data Scientist']);
    }
}
