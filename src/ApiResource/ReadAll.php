<?php

namespace ProjetNormandie\ForumBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ProjetNormandie\ForumBundle\Controller\MarkAdReadAll;

#[ApiResource(
    shortName: 'ForumForum',
    operations: [
        new Post(
            uriTemplate: '/forum_forums/read-all',
            controller: MarkAdReadAll::class,
            security: 'is_granted("ROLE_USER")',
        ),
    ],
)]

class ReadAll
{
}
