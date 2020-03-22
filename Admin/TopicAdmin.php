<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;

/**
 * Administration manager for the Forum Bundle.
 */
class TopicAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_topic';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export')
            ->remove('create');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('id', 'text', ['label' => 'id', 'attr' => ['readonly' => true]])
            ->add('libTopic', 'text', ['label' => 'libTopic'])
            ->add('forum')
            ->add('type');
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libTopic')
            ->add('forum', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libForum',
            ]);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('language')
            ->add('libTopic', null, ['label' => 'libTopic'])
            ->add('type')
            ->add('forum')
            ->add('user')
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('id')
            ->add('language')
            ->add('libTopic')
            ->add('type')
            ->add('forum')
            ->add('user');
    }
}
