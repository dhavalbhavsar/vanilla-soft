<?php

namespace App\Helpers;

use App\Utilities\Contracts\RedisHelperInterface;
use Illuminate\Support\Facades\Redis;

class RedisHelper implements RedisHelperInterface
{
    CONST RECENT_MESSAGE_KEY = 'recent_messages';
    public function storeRecentMessage(mixed $id, string $messageSubject, string $toEmailAddress, string $body): void
    {
        $message = [
            'id' => $id,
            'subject' => $messageSubject,
            'email' => $toEmailAddress,
            'body' => $body
        ];

        //Store message in redis
        Redis::lpush(self::RECENT_MESSAGE_KEY, json_encode($message));
    }

    public function getAllRecentMessage()
    {
        return Redis::lrange(self::RECENT_MESSAGE_KEY, 0, -1);
    }
}
