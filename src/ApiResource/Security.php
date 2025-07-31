<?php

namespace ProjetNormandie\ForumBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model;
use ProjetNormandie\ForumBundle\Controller\ReadAll;

#[ApiResource(
    shortName: 'ForumForum',
    operations: [
        new Get(
            uriTemplate: '/forum_forums/read-all',
            controller: ReadAll::class,
            read: false,
            security: 'is_granted("ROLE_USER")',
            openapi: new Model\Operation(
                summary: 'Mark all forums as read',
                description: 'Mark all forums as read'
            ),
        )
    ],
)]

class Security
{
}
