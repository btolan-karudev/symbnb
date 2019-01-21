<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PaginationService
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
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
     * @param int $curentPage
     * @return PaginationService
     */
    public function setCurentPage(int $curentPage): PaginationService
    {
        $this->curentPage = $curentPage;
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
}