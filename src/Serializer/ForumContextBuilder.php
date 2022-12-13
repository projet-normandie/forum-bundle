<?php
namespace ProjetNormandie\ForumBundle\Serializer;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
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

        if (($context['request_uri'] == '/api/categorie/home')
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER')
            && true === $normalization) {
            $context['groups'][] = 'forum.forum.forumUser1';
            $context['groups'][] = 'forum.forumUser.read';
        }

        if ($resourceClass === Forum::class
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER')
            && true === $normalization) {
            $context['groups'][] = 'forum.forum.forumUser1';
            $context['groups'][] = 'forum.ForumUser.read';
        }
        return $context;
    }
}
