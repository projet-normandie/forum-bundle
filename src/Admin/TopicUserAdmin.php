<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;

/**
 * Administration manager for the Forum Bundle.
 */
class TopicUserAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_topicUser';

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
        $form->add('topic', null, ['label' => 'label.topic'])
            ->add('user', null, ['label' => 'label.user'])
            ->add('boolRead', null, ['label' => 'label.boolRead'])
            ->add('boolNotif', null, ['label' => 'label.boolNotif']);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('user', null, ['label' => 'label.user'])
            ->add('topic', ModelFilter::class, [
                'label' => 'label.topic',
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property'=>'libTopic'],
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('topic', null, ['label' => 'label.topic'])
            ->addIdentifier('user', null, ['label' => 'label.user'])
            ->add('boolRead', null, ['label' => 'label.boolRead'])
            ->add('boolNotif', null, ['label' => 'label.boolNotif'])
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('topic', null, ['label' => 'label.topic'])
            ->add('user', null, ['label' => 'label.user']);
    }
}
