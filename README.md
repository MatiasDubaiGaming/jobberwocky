# Jobberwocky

This project utilizes Laravel as its PHP framework, leveraging Laravel Sail to simplify the setup and management of the development environment using Docker.

## Prerequisites

Before starting, make sure you have Docker and Docker Compose installed on your system. Laravel Sail is a command-line interface for interacting with the Docker environment provided by Laravel.

## Initial Setup

1. **Clone the Repository**

   First, clone the Jobberwocky repository to your local machine using Git:

   ```bash
   git clone https://github.com/MatiasDubaiGaming/jobberwocky.git
   cd jobberwocky
    ```
2. **Install Dependencies**

    With Composer , run the following commands to install PHP and JavaScript dependencies:

    ```bash
    composer install
    ```
3. **Configure .env File**
   Copy the .env.example file to .env and generate an application key:

   ```bash
   cp .env.example .env
   sail artisan key:generateç
     ```
   Make sure to review and adjust the settings in the .env file as necessary, especially those related to the database and other services.


4. **Start Laravel Sail**
   Laravel Sail provides a wrapper command in ./vendor/bin/sail. To simplify its use, you can add a temporary alias to your shell:

    ```bash
   alias sail='bash vendor/bin/sail'
    ```
   This command will build and start all the necessary containers for the development environment.
    ```bash
   sail up -d
    ```
    This command will build and start all the necessary containers for the development environment.
5. **Migrations and Seeders**
   Once the containers are up and running, execute the database migrations and seeders:
    
        sail artisan migrate --seed
6. **Accessing the Project**

   After completing the above steps, the Jobberwocky project should be accessible through http://localhost
   For more information on how to use Laravel Sail, refer to the official documentation: https://laravel.com/docs/11.x/sail


### Generate the Swagger documentation
```bash
sail artisan l5-swagger:generate
```

Access the Swagger UI at http://localhostl/api/documentation


