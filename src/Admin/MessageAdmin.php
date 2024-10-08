<?php

declare(strict_types=1);

namespace ProjetNormandie\ForumBundle\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Administration manager for the Forum Bundle.
 */
class MessageAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnf_admin_message';

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
            ->add('message', CKEditorType::class, [
                    'label' => 'label.message',
                    'required' => true,
                    'config' => array(
                        'height' => '400',
                        'toolbar' => 'standard'
                    ),
                ]);
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'label.id'])
            ->add('topic', ModelFilter::class, [
                'label' => 'label.topic',
                'field_type' => ModelAutocompleteType::class,
                'field_options' => ['property' => 'libTopic'],
            ]);
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('message', null, ['label' => 'label.message'])
            ->add('user', null, ['label' => 'label.user'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id', null, ['label' => 'label.id'])
            ->add('topic', null, ['label' => 'label.topic'])
            ->add('user', null, ['label' => 'label.user'])
            ->add('message', null, ['label' => 'label.message', 'safe' => true]);
    }
}
