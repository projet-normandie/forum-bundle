<?php

namespace ProjetNormandie\ForumBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use ProjetNormandie\ForumBundle\Controller\GetHomeForums;

#[ApiResource(
    shortName: 'ForumForum',
    operations: [
        new Get(
            uriTemplate: '/forum_forums/get-home',
            controller: GetHomeForums::class,
            read: false,
            normalizationContext: [
                'groups' => ['category:read', 'category:forums', 'forum:read', 'forum:last-message', 'message:user',
                    'user:read', 'forum:forum-user-1', 'forum-user:read', 'message:read'
                ]
            ],
            openapi: new Model\Operation(
                summary: 'Retrives list of home forums',
                description: 'Retrives list of home forums',
            ),
        ),
    ],
)]

class Forum
{
}
