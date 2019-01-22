<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    /**
     * PaginationService constructor.
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param $templatePath
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route

        ]);
    }

    public function getPages()
    {
        # 1. Connaitre le totl des enregistrements de la table #
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        # 2. Faire la division, l arrondi et le renvoyer #
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function getData()
    {
        # 1. Calculer l offset #
        $offset = $this->currentPage * $this->limit - $this->limit;

        # 2. Demander au repo de trouver les elements #
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        # 3. Renvoyer les elements demandee #
        return $data;

    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $entityClass
     * @return PaginationService
     */
    public function setEntityClass($entityClass): PaginationService
    {
        $this->entityClass = $entityClass;

        return $this;
    }


    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return PaginationService
     */
    public function setCurrentPage(int $currentPage): PaginationService
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return PaginationService
     */
    public function setLimit(int $limit): PaginationService
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param mixed $route
     * @return PaginationService
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $templatePath
     * @return PaginationService
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}