<p align="center"><a href="https://autoklose.com" target="_blank"><img src="https://app.autoklose.com/images/svg/autoklose-logo-white.svg" width="400"></a></p>

## Goal
The primary goal is for the functionality to work as expected. The idea is to spend about 4 working hours on it, a maximum of 8 working hours.

## Minimum requirements
- Have an endpoint as described above that accepts an array of emails, each of them having a subject, body, and the email address where is the email going to
- Build a mail using a standard set of Laravel functions for it and the default email provider (the one that is easiest for you to setup)
- Build a job to dispatch email, use the default Redis/Horizon setup
- Store information about the sent email in Elasticsearch using a class that implements the ElasticsearchHelperInterface provided. (This interface can be modified however you see fit.)
- Cache the stored information in Redis using a class that implements the RedisHelperInterface provided. (This interface can be modified however you see fit.)
- Write a unit test that makes sure that the job is dispatched correctly and also is not dispatched if there’s a validation error


## Bonus requirements
- Have an endpoint api/list that lists all sent emails with email, subject, body
- Unit test the above-mentioned route (test for expected subject/body)
- Upgrade the project from Laravel 9 to Laravel 10

## Installation
- git clone
- move to project directory
- composer install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- sail build && sail up
- php artisan horizon (optional for local - for queueable job)

## API Routes
Metod | API                                                                  | params
--- |----------------------------------------------------------------------| ---
Post | api/{user}/send?api_token={{your_api_token}}                         | `{"emails": [{"requestEmail": "dhaval.s.bhavsar@gmail.com","subject": "Test Subject 1","body": "I am test subject body"},{"requestEmail": "test.user1@gmail.com","subject": "Test Subject 2","body": "I am test subject body"},{"requestEmail": "test.user2@gmail.com","subject": "Test Subject 3","body": "I am test subject body"}]}`
Get | api/list?api_token={{your_api_token}}&search=dhaval |

## PHPUnitTest

- Run the `php artisan test` to run all unit test or if you want to run specific test `php artisan test --filter <Name of Test>`


Here are list of Unit tests

Name | Description | Command
--- |----------------------------------------------------------------------| ---
testMailIsQueuedAsJob | Check Email set for queue | `php artisan test --filter testMailIsQueuedAsJob`

## Note

Please find the `your_api_token` from .env.example for making auth process simple.






