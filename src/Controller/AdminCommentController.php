<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     * @param CommentRepository $repo
     * @param PaginationService $pagination
     * @return Response
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
            ->setLimit(5)
            ->setCurrentPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de editer un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();


            $this->addFlash(
                'success',
                "Le commentaire relie a l annonce
                <strong>{$comment->getAd()->getTitle()}</strong>
                 a bien été modifiée !"
            );

            return $this->redirectToRoute('admin_comments_index');

        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de suprimer un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
            $manager->remove($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a bien été supprimée !"
            );

        return $this->redirectToRoute('admin_comments_index');
    }
}
