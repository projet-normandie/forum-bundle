<?php
namespace ProjetNormandie\ForumBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;

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

        if ($resourceClass === Topic::class
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER') && true === $normalization) {
            $context['groups'][] = 'forum.topic.topicUser1';
            $context['groups'][] = 'forum.topicUser.read';
        }

        return $context;
    }
}
