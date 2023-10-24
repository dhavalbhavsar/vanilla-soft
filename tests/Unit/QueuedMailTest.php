<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\EmailMailable;
use App\Jobs\SendEmailJob;

class QueuedMailTest extends TestCase
{
    public function testMailIsQueuedAsJob()
    {
        $fakeTitle = 'This is test';
        $fakeBody = 'I am test body';
        $fakeEmail = 'info@test.com';

        Bus::fake();

        SendEmailJob::dispatch($fakeEmail, $fakeTitle, $fakeBody);

        // Assert that the mailable was dispatched as a job
        Bus::assertDispatched(SendEmailJob::class);

    }
}
