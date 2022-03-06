<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Administration manager for the Forum Bundle.
 */
class TopicAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_topic';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export')
            ->remove('create');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('id', TextType::class, ['label' => 'label.id', 'attr' => ['readonly' => true]])
            ->add('libTopic', TextType::class, ['label' => 'label.topic'])
            ->add('boolArchive')
            ->add('forum')
            ->add('type');
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('forum', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libForum',
            ])
            ->add('boolArchive');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('type', null, ['label' => 'label.type'])
            ->add(
                'boolArchive',
                null,
                [
                    'label' => 'label.boolArchive',
                    'editable' => true,
                ]
            )
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('user', null, ['label' => 'label.user'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'label.id'])
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('boolArchive', null, ['label' => 'label.boolArchive'])
            ->add('nbMessage', null, ['label' => 'label.nbMessage'])
            ->add('lastMessage', null, ['label' => 'label.lastMessage'])
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('user', null, ['label' => 'label.user']);
    }
}
