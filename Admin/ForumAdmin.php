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
class ForumAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_forum';

    /**
     * @inheritdoc
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('idForum', 'text', ['label' => 'idForum', 'attr' => ['readonly' => true]])
            ->add('libForum', 'text', ['label' => 'libForum'])
            ->add('category')
            ->add('position', 'text', ['label' => 'position', 'required' => true]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category');
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('idForum')
            ->add('libForum', null, ['label' => 'libForum'])
            ->add('category')
            ->add('position', null, ['label' => 'position'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('idForum')
            ->add('libForum')
            ->add('position');
    }
}
