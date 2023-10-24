<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function __construct(
        private ElasticsearchHelperInterface $elasticsearchHelper,
        private RedisHelperInterface $redisHelper,
        Request $request
    ) {
        $token = $request->query('api_token', '');
        if ($token !== config('app.api_token')) {
            throw new ApiException('Unauthorized', 401);
        }
    }

    // Send method
    public function send(Request $request, User $user)
    {
        $emails = $request->get('emails', []);

        //This job for send asynchronous email to give email
        try {
            foreach ($emails as $email) {

                SendEmailJob::dispatch($email['requestEmail'], $email['subject'], $email['body']);

                /** @var ElasticsearchHelperInterface $elasticsearchHelper */
                $storeEmail = $this->elasticsearchHelper->storeEmail($email['body'], $email['subject'], $email['requestEmail']);

                /** @var RedisHelperInterface $redisHelper */
                $this->redisHelper->storeRecentMessage($storeEmail['_id'], $email['subject'], $email['requestEmail'], $email['body']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail']);
        }

        return response()->json(['status' => 'success']);
    }

    // List
    public function list(Request $request)
    {
        $search = $request->query('search', null);
        $result = [];

        if (empty($search)) {
            /** @var RedisHelperInterface $redisHelper */
            $allMessages = $this->redisHelper->getAllRecentMessage();
            $result = array_map(function($message){
                $messageArr = json_decode($message, true);

                return [
                    'email' => $messageArr['email'],
                    'subject' => $messageArr['subject'],
                    'body' => $messageArr['body'],
                ];

            }, $allMessages);
        } else {
            /** @var ElasticsearchHelperInterface $elasticsearchHelper */
            $elasticSearchResult = $this->elasticsearchHelper->search($search);
            if ($elasticSearchResult['hits']['total']['value'] > 0) {
                $result = array_map(function($message){
                    return [
                        'email' => $message['_source']['toEmail'],
                        'subject' => $message['_source']['subject'],
                        'body' => $message['_source']['body'],
                    ];
                }, $elasticSearchResult['hits']['hits']);
            }
        }

        return response()->json(['status' => 'success', 'data' => $result]);
    }
}
