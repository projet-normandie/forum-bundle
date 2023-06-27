<?php

namespace ProjetNormandie\ForumBundle\Service;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ProjetNormandie\ForumBundle\Entity\Forum;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Repository\ForumUserRepository;
use ProjetNormandie\ForumBundle\Repository\TopicUserRepository;
use Symfony\Component\Security\Core\Security;

class MarkAsReadService
{
    private Security $security;
    private ForumUserRepository $forumUserRepository;
    private TopicUserRepository $topicUserRepository;

    /**
     * @param Security            $security
     * @param ForumUserRepository $forumUserRepository
     * @param TopicUserRepository $topicUserRepository
     */
    public function __construct(
        Security $security,
        ForumUserRepository $forumUserRepository,
        TopicUserRepository $topicUserRepository
    )
    {
        $this->security = $security;
        $this->forumUserRepository = $forumUserRepository;
        $this->topicUserRepository = $topicUserRepository;
    }


    /**
     * @return void
     */
    public function readAlL(): void
    {
        $user = $this->security->getUser();
        $this->forumUserRepository->markAsRead($user);
        $this->topicUserRepository->markAsRead($user);
    }

    /**
     * @param Forum $forum
     * @return void
     */
    public function readForum(Forum $forum): void
    {
        $user = $this->security->getUser();

        $this->topicUserRepository->markAsRead($user, null, $forum);
        $this->forumUserRepository->markAsRead($user, $forum);
        if (null !== $forum->getParent()) {
            $this->forumUserRepository->markAsRead($user, $forum->getParent());
        }
    }

    /**
     * @param Topic $topic
     * @return void
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function readTopic(Topic $topic): void
    {
        $user = $this->security->getUser();

        $isRead = $this->topicUserRepository->isRead($user, $topic);
        if (true === $isRead) {
            return;
        }

        $this->topicUserRepository->markAsRead($user, $topic);

        $forum = $topic->getForum();
        $nbTopicNotRead = $this->topicUserRepository->countTopicNotRead($user, $forum);
        if (0 === $nbTopicNotRead) {
            $this->forumUserRepository->markAsRead($user, $topic->getForum());
            if ($forum->getParent()) {
                 $nbSubForumNotRead = $this->forumUserRepository->countSubForumNotRead($user, $forum->getParent());
                if (0 === $nbSubForumNotRead) {
                    $this->forumUserRepository->markAsRead($user, $forum->getParent());
                }
            }
        }
    }
}
