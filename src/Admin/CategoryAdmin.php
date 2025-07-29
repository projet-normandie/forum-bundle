<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
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
    protected $baseRouteName = 'pnf_admin_category';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('export');
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('id', TextType::class, ['label' => 'label.id', 'attr' => ['readonly' => true]])
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('position', TextType::class, ['label' => 'label.position', 'required' => false]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'label.id'])
            ->add('name', null, ['label' => 'label.name'])
            ->add('position', null, ['label' => 'label.position']);
    }
}
