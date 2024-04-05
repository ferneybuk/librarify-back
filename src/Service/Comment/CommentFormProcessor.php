<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Form\Model\CommentDto;
use App\Form\Type\CommentFormType;
use App\Repository\CommentRepository;
use App\Service\Utils\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentFormProcessor
{
    public function __construct(
        private GetComment $getComment,
        private CommentRepository $commentRepository,
        private FormFactoryInterface $formFactory,
        private Security $security
    )
    {
    }

    public function __invoke(Request $request, ?string $commentId = null): array
    {
        $comment = null;
        $commentDto = null;

        if ($commentId === null) {
            $commentDto = new CommentDto();
        } else {
            $comment = ($this->getComment)($commentId);
            $commentDto = CommentDto::createFromComment($comment);
        }

        $form = $this->formFactory->create(CommentFormType::class, $commentDto);
        $content = json_decode($request->getContent(), true);
        $form->submit($content);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if (!$form->isValid()) {
            return [null, $form];
        }

        if ($comment === null) {
            $user = $this->security->getCurrentUser();
            $comment = Comment::create(
                $commentDto->name,
                $user
            );
        } else {
            $comment->update(
                $comment->name
            );
        }

        $this->commentRepository->save($comment);
        return [$comment, null];
    }
}