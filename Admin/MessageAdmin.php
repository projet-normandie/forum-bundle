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
class MessageAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_message';

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
            ->add('message', 'textarea', ['label' => 'Message', 'attr' => ['rows' => 20]]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('topic', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libTopic',
            ]);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('message', null, ['label' => 'Message'])
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
            ->add('topic')
            ->add('user')
            ->add('message', null, ['label' => 'Message', 'safe' => true]);
    }
}
