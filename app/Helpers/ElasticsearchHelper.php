<?php

namespace App\Helpers;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Elasticsearch\Client;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    CONST SEARCH_INDEX = 'emails';

    CONST SEARCH_TYPE = 'email';

    protected $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): array
    {
        return $this->elasticsearch->index([
            'index' => self::SEARCH_INDEX,
            'type' => self::SEARCH_TYPE,
            'body' => [
                'body' => $messageBody,
                'subject' => $messageSubject,
                'toEmail' => $toEmailAddress
            ],
        ]);
    }

    public function search(string $query): array
    {
        $params = [
            'index' => self::SEARCH_INDEX,
            'type' => self::SEARCH_TYPE,
            'body' => [
                'query' => [
                    'query_string' => [
                        'query' => '*' . $query . '*',
                        'default_field' => '*',
                    ],
                ],
                'size' => 10000,
            ],
        ];

        return $this->elasticsearch->search($params);
    }
}
