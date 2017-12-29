<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

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
        $formMapper->add('idTopic', 'text', ['label' => 'idTopic', 'attr' => ['readonly' => true]])
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
            ->add('forum');
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('idTopic')
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
        $showMapper->add('idTopic')
            ->add('libTopic')
            ->add('type')
            ->add('forum')
            ->add('user');
    }
}
