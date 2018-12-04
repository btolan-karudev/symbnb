<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig',
            [
                'ads' => $ads
            ]);
    }

    /**
     * Permet la creation dune annonce
     *
     * @Route("/ads/new", name="ads_create")
     *
     * @return Response
     */
    public function create()
    {
        $ad = new Ad();

        $form = $this->createForm(AnnonceType::class, $ad);


        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * Afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @param Ad $ad
     * @return Response
     */
    public function show(Ad $ad)
    {
        return $this->render('ad/show.html.twig',
            [
                'ad' => $ad
            ]);
    }


}
