<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use ProjetNormandie\ForumBundle\Entity\Category;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class TopicContextBuilder implements SerializerContextBuilderInterface
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

        if (
            $resourceClass === Topic::class
            && isset($context['groups'])
            && true === $normalization
        ) {
            $baseGroups = $context['groups'];

            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                $baseGroups[] = 'topic:read-status';
            }

            $context['groups'] = array_unique($baseGroups);
        }
        return $context;
    }
}
