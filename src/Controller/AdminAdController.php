<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index")
     * @param AdRepository $repo
     * @param int $page
     * @return Response
     */
    public function index(AdRepository $repo, $page = 1)
    {
        $limit = 10;
        $start = $page * $limit - $limit;

        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findBy([], [], $limit, $start)
        ]);
    }

    /**
     * Permet de editer une annonce
     *
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );
        }


        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de suprimer une annonce
     *
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager)
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'warning',
                "Vous ne pouvais pas supprimer l'annonce 
                    <strong>{$ad->getTitle()}</strong>
                car elle possede deja des reservations en cours"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !"
            );
        }


        return $this->redirectToRoute('admin_ads_index');
    }
}
