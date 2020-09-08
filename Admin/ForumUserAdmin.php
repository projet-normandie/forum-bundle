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
class ForumUserAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_forumUser';

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
        $formMapper->add('forum')
            ->add('user')
            ->add('boolRead');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('forum', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libForum',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('forum')
            ->addIdentifier('user')
            ->add('boolRead')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('forum')
            ->add('user');
    }
}
