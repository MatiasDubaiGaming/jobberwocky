@component('mail::message')
    # New Job Created

    A new job has been created with the following details:

    - **Title:** {{ $jobListing->title }}
    - **Company:** {{ $jobListing->company }}
    - **Location:** {{ $jobListing->location }}
    - **Description:** {{ $jobListing->description }}

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
