<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Controller\Topic;

use Doctrine\ORM\EntityManagerInterface;
use ProjetNormandie\ForumBundle\Entity\Topic;
use ProjetNormandie\ForumBundle\Entity\TopicUserLastVisit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class ToggleNotification extends AbstractController
{
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * Toggle la notification pour un topic donné
     */
    public function __invoke(Topic $topic): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => $this->translator->trans('error.authentication_required')
            ], 401);
        }

        try {
            $this->em->beginTransaction();

            // Récupérer ou créer la visite du topic
            $topicVisit = $this->em->getRepository(TopicUserLastVisit::class)
                ->findOneBy(['user' => $user, 'topic' => $topic]);

            if (!$topicVisit) {
                // Créer une nouvelle visite si elle n'existe pas
                $topicVisit = new TopicUserLastVisit();
                $topicVisit->setUser($user);
                $topicVisit->setTopic($topic);
                // La date de visite sera définie automatiquement dans le constructeur
                $this->em->persist($topicVisit);
            }

            // Toggle la notification
            $currentNotificationStatus = $topicVisit->getIsNotify();
            $newNotificationStatus = !$currentNotificationStatus;
            $topicVisit->setIsNotify($newNotificationStatus);

            $this->em->flush();
            $this->em->commit();

            return new JsonResponse([
                'success' => true,
                'isNotify' => $newNotificationStatus,
                'message' => $this->translator->trans($newNotificationStatus ?
                    'topic.notification.enabled' :
                    'topic.notification.disabled')
            ]);

        } catch (\Exception $e) {
            $this->em->rollback();
            return new JsonResponse([
                'success' => false,
                'error' => $this->translator->trans('error.notification_update_failed')
            ], 500);
        }
    }
}
