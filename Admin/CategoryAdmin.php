<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Administration manager for the Forum Bundle.
 */
class CategoryAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_category';

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
        $formMapper->add('id', TextType::class, ['label' => 'label.id', 'attr' => ['readonly' => true]])
            ->add('libCategory', TextType::class, ['label' => 'label.category'])
            ->add('position', TextType::class, ['label' => 'label.position', 'required' => false]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libCategory', null, ['label' => 'label.category'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('id', null, ['label' => 'label.id'])
            ->add('libCategory', null, ['label' => 'label.category'])
            ->add('position', null, ['label' => 'label.position']);
    }
}
