<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Administration manager for the Forum Bundle.
 */
class TopicAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'pnforumbundle_admin_topic';

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
        $formMapper->add('id', TextType::class, ['label' => 'label.id', 'attr' => ['readonly' => true]])
            ->add('libTopic', TextType::class, ['label' => 'label.topic'])
            ->add('forum')
            ->add('type');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('forum', ModelAutocompleteFilter::class, [], null, [
                'property' => 'libForum',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id', null, ['label' => 'label.id'])
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('user', null, ['label' => 'label.user'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('id', null, ['label' => 'label.id'])
            ->add('libTopic', null, ['label' => 'label.topic'])
            ->add('type', null, ['label' => 'label.type'])
            ->add('forum', null, ['label' => 'label.forum'])
            ->add('user', null, ['label' => 'label.user']);
    }
}
