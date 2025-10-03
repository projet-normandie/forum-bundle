<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use ProjetNormandie\ForumBundle\Controller\GetStats;

#[ApiResource(
    shortName: 'ForumStats',
    operations: [
        new Get(
            uriTemplate: '/forum_stats',
            controller: GetStats::class,
            read: false,
            /*openapi: new Model\Operation(
                summary: 'Get forum statistics',
                description: 'Returns global forum statistics including number of forums, topics, messages, active users and today\'s activity',
                parameters: [
                    [
                        'name' => 'extended',
                        'in' => 'query',
                        'required' => false,
                        'schema' => ['type' => 'boolean'],
                        'description' => 'Include extended statistics (week activity, top users, forum breakdown)'
                    ],
                    [
                        'name' => 'refresh',
                        'in' => 'query',
                        'required' => false,
                        'schema' => ['type' => 'boolean'],
                        'description' => 'Force refresh of cached statistics'
                    ]
                ],
                responses: [
                    '200' => [
                        'description' => 'Forum statistics',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'nbForum' => [
                                            'type' => 'integer',
                                            'description' => 'Total number of public forums'
                                        ],
                                        'nbTopic' => [
                                            'type' => 'integer',
                                            'description' => 'Total number of non-archived topics in public forums'
                                        ],
                                        'nbMessage' => [
                                            'type' => 'integer',
                                            'description' => 'Total number of messages in public forums'
                                        ],
                                        'activeUsers' => [
                                            'type' => 'integer',
                                            'description' => 'Number of active users in the last 24 hours'
                                        ],
                                        'todayActivity' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'nbNewTopic' => [
                                                    'type' => 'integer',
                                                    'description' => 'Number of new topics created today'
                                                ],
                                                'nbNewMessage' => [
                                                    'type' => 'integer',
                                                    'description' => 'Number of new messages created today'
                                                ]
                                            ]
                                        ],
                                        'lastUpdate' => [
                                            'type' => 'string',
                                            'format' => 'date-time',
                                            'description' => 'Timestamp of when statistics were generated'
                                        ],
                                        'weekActivity' => [
                                            'type' => 'object',
                                            'description' => 'Week activity (only when extended=true)',
                                            'properties' => [
                                                'nbNewTopicWeek' => [
                                                    'type' => 'integer',
                                                    'description' => 'Number of new topics created this week'
                                                ],
                                                'nbNewMessageWeek' => [
                                                    'type' => 'integer',
                                                    'description' => 'Number of new messages created this week'
                                                ]
                                            ]
                                        ],
                                        'topActiveUsers' => [
                                            'type' => 'array',
                                            'description' => 'Top active users (only when extended=true)',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'user_id' => ['type' => 'integer'],
                                                    'activity_count' => ['type' => 'integer']
                                                ]
                                            ]
                                        ],
                                        'forumBreakdown' => [
                                            'type' => 'array',
                                            'description' => 'Forum breakdown (only when extended=true)',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'name' => ['type' => 'string'],
                                                    'slug' => ['type' => 'string'],
                                                    'nbTopic' => ['type' => 'integer'],
                                                    'nbMessage' => ['type' => 'integer']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ),*/
        )
    ],
)]
class Stats
{
}
