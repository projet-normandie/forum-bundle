<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use ProjetNormandie\ForumBundle\Entity\Category;
use ProjetNormandie\ForumBundle\Entity\Forum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ForumContextBuilder implements SerializerContextBuilderInterface
{
    private SerializerContextBuilderInterface $decorated;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        // Gestion du endpoint GetHome pour Category
        if (
            $resourceClass === Category::class
            && str_contains($request->getRequestUri(), '/forum_category/get-home')
            && isset($context['groups'])
            && true === $normalization
        ) {
            // Groups de base pour tous (guests et users)
            $baseGroups = [
                'category:read',
                'category:forums',
                'forum:read',
                'forum:last-message',
                'message:read',
                'message:user',
                'user:read:minimal',
            ];

            // Si l'utilisateur est connecté, ajouter les groups spécifiques
            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                $baseGroups[] = 'forum:forum-user-1';
                $baseGroups[] = 'forum-user:read';
            }

            $context['groups'] = $baseGroups;
        }

        /*if (
            $resourceClass === Forum::class
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER')
            && true === $normalization
        ) {
            $context['groups'][] = 'forum.forum.forumUser1';
            $context['groups'][] = 'forum.ForumUser.read';
        }*/
        return $context;
    }
}
