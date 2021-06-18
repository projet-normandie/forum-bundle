<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use ProjetNormandie\ForumBundle\Entity\Forum;

/**
 * Administration manager for the Forum Bundle.
 */
class ForumAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_forum';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('id', TextType::class, ['label' => 'id', 'attr' => ['readonly' => true]])
            ->add('libForum', TextType::class, ['label' => 'label.forum'])
            ->add('libForumFr', TextType::class, ['label' => 'label.forumFr'])
            ->add('category')
            ->add('isParent', CheckboxType::class, [
                'required' => false,
            ])
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => Forum::getStatusChoices(),
                ]
            )
            ->add('position', TextType::class, ['label' => 'label.position', 'required' => true]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category', null, ['label' => 'label.category'])
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('status', null, ['label' => 'label.status']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('category', null, ['label' => 'label.category'])
            ->add('isParent', null, ['label' => 'label.isParent'])
            ->add('parent', null, ['label' => 'label.parent'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('id')
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('libForumFr', null, ['label' => 'label.forumFr'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('nbTopic', null, ['label' => 'label.nbTopic'])
            ->add('nbMessage', null, ['label' => 'label.nbMessage'])
            ->add('lastMessage', null, ['label' => 'label.lastMessage']);
    }
}
