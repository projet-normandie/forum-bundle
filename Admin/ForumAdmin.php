<?php

namespace ProjetNormandie\ForumBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use ProjetNormandie\ForumBundle\Entity\Forum;

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
        $formMapper->add('id', TextType::class, ['label' => 'id', 'attr' => ['readonly' => true]])
            ->add('libForum', TextType::class, ['label' => 'label.forum'])
            ->add('category')
            ->add(
                'status',
                ChoiceType::class,
                [
                    'label' => 'label.status',
                    'choices' => Forum::getStatusChoices(),
                ]
            )
            ->add('position', TextType::class, ['label' => 'label.position', 'required' => true]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category', null, ['label' => 'label.category'])
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('status', null, ['label' => 'label.status']);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('category', null, ['label' => 'label.category'])
            ->add('status', null, ['label' => 'label.status'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException When defining wrong or duplicate field names.
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('id')
            ->add('libForum', null, ['label' => 'label.forum'])
            ->add('position', null, ['label' => 'label.position'])
            ->add('topics', null, ['label' => 'label.topics']);
    }
}
