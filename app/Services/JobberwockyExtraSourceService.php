<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class JobberwockyExtraSourceService
{
    protected Client $client;
    protected string $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = env('JOBBERWOCKY_EXTRA_SOURCE_URL');
    }

    public function fetchJobs(Request $request): array
    {
        $url = $this->url . '/jobs?' . $request->getQueryString();

        $response = $this->client->get($url);
        $externalJobs = json_decode($response->getBody(), true);

        return $this->processJobs($externalJobs);
    }

    protected function processJobs($jobs): array
    {
        $data = [];

        $title = 0;
        $salary = 1;
        $skills = 2;

        foreach ($jobs as $country => $jobList) {
            foreach ($jobList as $job) {
                $data[] = [
                    'title' => $job[$title],
                    'salary' => $job[$salary],
                    'skills' => simplexml_load_string($job[$skills]),
                    'location' => $country,
                ];
            }
        }

        return $data;
    }
}
