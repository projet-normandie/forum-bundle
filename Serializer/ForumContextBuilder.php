<?php
namespace ProjetNormandie\ForumBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ForumContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $authorizationChecker;

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
            && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_USER')
            && true === $normalization) {
            $context['groups'][] = 'forum.user.read';
        }

        if ($resourceClass === Forum::class
            && isset($context['groups'])
            && $this->authorizationChecker->isGranted('ROLE_USER') && true === $normalization) {
            $context['groups'][] = 'forum.forum.user.read.1';
        }
        return $context;
    }
}
