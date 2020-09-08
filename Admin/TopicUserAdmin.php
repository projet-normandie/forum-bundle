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
class TopicUserAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_topicUser';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export')
            ->remove('create');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('topic')
            ->add('user')
            ->add('boolRead')
            ->add('boolNotif');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('topic', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libTopic',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('topic')
            ->addIdentifier('user')
            ->add('boolRead')
            ->add('boolNotif')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('topic')
            ->add('user');
    }
}
