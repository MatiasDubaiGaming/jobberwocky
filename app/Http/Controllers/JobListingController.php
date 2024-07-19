<?php

namespace App\Http\Controllers;

use App\Events\JobCreated;
use App\Models\JobListing;
use App\Services\JobberwockyExtraSourceService;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="Documentation for Jobberwocky API",
 *     @OA\Contact(
 *         email="lancrymatias@live.com",
 *         name="Matias Lancry"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="http://opensource.org/licenses/MIT"
 *     )
 * )
 */
class JobListingController extends Controller
{

    protected JobberwockyExtraSourceService $jobberwockyService;

    public function __construct( JobberwockyExtraSourceService $jobberwockyService)
    {
        $this->jobberwockyService = $jobberwockyService;
    }


    /**
     * @OA\Get(
     *     path="/api/job_listings",
     *     operationId="getJobListings",
     *     tags={"Job Listings"},
     *     summary="Get list of job listings",
     *     description="Returns a list of job listings. You can filter the results by title and location.",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter by job title",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="Filter by job location",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try{
            $query = JobListing::query();

            if ($request->has('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            return response()->json($query->get(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Not Found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/job_listings",
     *     operationId="createJobListing",
     *     tags={"Job Listings"},
     *     summary="Create a new job listing",
     *     description="Creates a new job listing with the provided details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "company", "skills", "location"},
     *             @OA\Property(property="title", type="string", example="Software Engineer"),
     *             @OA\Property(property="description", type="string", example="Full-time software engineering position"),
     *             @OA\Property(property="company", type="string", example="Tech Company"),
     *             @OA\Property(property="skills", type="string", example="PHP, JavaScript, MySQL"),
     *             @OA\Property(property="location", type="string", example="Remote"),
     *             @OA\Property(property="salary", type="number", format="float", example=80000.00),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job listing created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="title", type="string", example="Software Engineer"),
     *             @OA\Property(property="description", type="string", example="Full-time software engineering position"),
     *             @OA\Property(property="company", type="string", example="Tech Company"),
     *             @OA\Property(property="skills", type="string", example="PHP, JavaScript, MySQL"),
     *             @OA\Property(property="location", type="string", example="Remote"),
     *             @OA\Property(property="salary", type="number", format="float", example=80000.00),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request: Missing required fields or invalid data",
     *     ),
     * )
     */
    public function create(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'company' => 'required',
                'skills' => 'required',
                'location' => 'required',
                'salary' => 'nullable|numeric',
            ]);
            $jobListing = JobListing::create($request->all());
            event(new JobCreated($jobListing));

            return response()->json($jobListing, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bad Request: Missing required fields or invalid data', 'message' => $e->getMessage() ], 400);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/job_listings/{id}",
     *     operationId="getJobListingById",
     *     tags={"Job Listings"},
     *     summary="Get a job listing by ID",
     *     description="Returns a single job listing based on the provided ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job listing",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="title", type="string", example="Software Engineer"),
     *             @OA\Property(property="description", type="string", example="Full-time software engineering position"),
     *             @OA\Property(property="company", type="string", example="Tech Company"),
     *             @OA\Property(property="skills", type="string", example="PHP, JavaScript, MySQL"),
     *             @OA\Property(property="location", type="string", example="Remote"),
     *             @OA\Property(property="salary", type="number", format="float", example=80000.00),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job listing not found",
     *     ),
     * )
     */
    public function show(string $id)
    {
        try{
            return JobListing::findOrFail($id)->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Job listing not found'], 404);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/job_listings/{id}",
     *     operationId="updateJobListing",
     *     tags={"Job Listings"},
     *     summary="Update a job listing by ID",
     *     description="Updates an existing job listing based on the provided ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job listing",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated job listing data",
     *         @OA\JsonContent(
     *             required={"title", "description", "company", "skills", "location"},
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="description", type="string", example="Updated description."),
     *             @OA\Property(property="company", type="string", example="Updated Company"),
     *             @OA\Property(property="skills", type="string", example="Updated Skills"),
     *             @OA\Property(property="location", type="string", example="Updated Location"),
     *             @OA\Property(property="salary", type="number", format="float", example=90000.00),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="title", type="string", example="Updated Title"),
     *             @OA\Property(property="description", type="string", example="Updated description."),
     *             @OA\Property(property="company", type="string", example="Updated Company"),
     *             @OA\Property(property="skills", type="string", example="Updated Skills"),
     *             @OA\Property(property="location", type="string", example="Updated Location"),
     *             @OA\Property(property="salary", type="number", format="float", example=90000.00),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request: Missing required fields or invalid data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job listing not found",
     *     ),
     * )
     */
    public function update(Request $request, int $id)
    {
        try{
            $jobListing = JobListing::findOrFail($id);
            $request->validate([
                'title' => 'sometimes|required',
                'description' => 'sometimes|required',
                'company' => 'sometimes|required',
                'skills' => 'sometimes|required',
                'location' => 'sometimes|required',
                'salary' => 'nullable|numeric',
            ]);
            $jobListing->update($request->all());

            return $jobListing->toJson();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Job listing not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/job_listings/{id}",
     *     operationId="deleteJobListing",
     *     tags={"Job Listings"},
     *     summary="Delete a job listing by ID",
     *     description="Deletes an existing job listing based on the provided ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job listing",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Successful operation: No content"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job listing not found"
     *     ),
     * )
     */

    public function destroy(string $id)
    {
        try{
            JobListing::destroyOrFail($id);

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Job listing not found', 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/job_listings/combined_jobs",
     *     operationId="getCombinedJobs",
     *     tags={"Job Listings"},
     *     summary="Get combined list of internal and external job listings",
     *     description="Returns a combined list of job listings from internal and external sources. You can filter the results by job title (`name`), minimum salary (`salary_min`), maximum salary (`salary_max`), and job location (`country`).",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by job title",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="salary_min",
     *         in="query",
     *         description="Filter by minimum salary",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="salary_max",
     *         in="query",
     *         description="Filter by maximum salary",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Filter by job location",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function combinedJobs(Request $request)
    {
        try {
            $query = JobListing::query();
            if ($request->has('name')) {
                $query->where('title', 'like', '%' . $request->name . '%');
            }
            if ($request->has('salary_min')) {
                $query->where('location', 'like', '%' . $request->country . '%');
            }
            if ($request->has('salary_max')) {
                $query->where('location', 'like', '%' . $request->country . '%');
            }
            if ($request->has('country')) {
                $query->where('location', 'like', '%' . $request->country . '%');
            }
            $internalJobs = $query->get()->toArray();

            $externalJobs = $this->jobberwockyService->fetchJobs($request);
            $combinedJobs = array_merge($internalJobs, $externalJobs);

            return response()->json($combinedJobs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/job_listings/external_jobs",
     *     operationId="fetchExternalJobs",
     *     tags={"Job Listings"},
     *     summary="Fetch external job listings",
     *     description="Returns a list of job listings from an external source. You can optionally provide query parameters to filter the results.",
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Filter by job title",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salary_min",
     *          in="query",
     *          description="Filter by minimum salary",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="salary_max",
     *          in="query",
     *          description="Filter by maximum salary",
     *          required=false,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="country",
     *          in="query",
     *          description="Filter by job location",
     *          required=false,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function fetchExternalJobs(Request $request)
    {
        try{
            $externalJobs = $this->jobberwockyService->fetchJobs($request);

            return response()->json($externalJobs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);

        }
    }
}
