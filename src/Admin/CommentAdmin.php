<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Comment;
use App\Repository\UserRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class CommentAdmin extends AbstractAdmin
{
    private ?UserRepository $userRepository;

    protected function createNewInstance(): object
    {
        if ($this->userRepository === null) {
            throw new \LogicException('Not user repository');
        }
        $user = $this->userRepository->findOneBy([]);
        if ($user === null) {
            throw new \LogicException('Create at least one user');
        }
        return Comment::create(
            content: '',
            user: $user,
            book: null
        );
    }

    public function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('content')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('content')
            ->add('createdAt')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id', null, ['disabled' => true])
            ->add('content')
            ->add('user')
            ->add('book')
            ->add('createdAt')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('content')
            ->add('user')
            ->add('book')
            ->add('createdAt')
        ;
    }
}
