<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 *
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
 * Class PaginationService
 * @package App\Service
 */
class PaginationService
{
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private $entityClass;

    /**
     *  Le nombre d'enregistrement à récupérer
     *
     * @var int
     */
    private $limit = 10;

    /**
     * a page sur laquelle on se trouve actuellement
     *
     * @var int
     */
    private $currentPage = 1;

    /**
     * Le manager de Doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var ObjectManager
     */
    private $manager;

    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Environment
     */
    private $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de la navigation
     *
     * @var mixed
     */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var
     */
    private $templatePath;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     *
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * PaginationService constructor.
     * @param ObjectManager $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param $templatePath
     */
    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        // Autres initialisations
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     *  Permet d'afficher le rendu de la navigation au sein d'un template twig !
     *
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     *
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

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     *
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     *
     * @return float
     * @throws Exception
     */
    public function getPages()
    {
        if (empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new Exception(
                "Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utiliser la methode setEntityClass() de votre objet PaginationService");
        }
        # 1. Connaitre le totl des enregistrements de la table #
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        # 2. Faire la division, l arrondi et le renvoyer #
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     *
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une
     * certaine limite et en partant d'un offset
     *
     * @return object[]
     * @throws Exception si la propriété $entityClass n'est pas définie
     */
    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new Exception(
                "Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utiliser la methode setEntityClass() de votre objet PaginationService");
        }

        # 1. Calculer l offset #
        $offset = $this->currentPage * $this->limit - $this->limit;

        # 2. Demander au repo de trouver les elements #
        // dans la limite d'éléments imposée (voir propriété $limit)
        return $this->manager
                    ->getRepository($this->entityClass)
                    ->findBy([], [], $this->limit, $offset);

    }

    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     * - App\Entity\Ad::class
     * - App\Entity\Comment::class
     *
     * @param mixed $entityClass
     * @return self
     */
    public function setEntityClass($entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }


    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param int $currentPage
     * @return self
     */
    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     *  Permet de récupérer le nombre d'enregistrements qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param mixed $route Le nom de la route à utiliser
     * @return self
     */
    public function setRoute($route): self
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisé sur les liens de la navigation
     *
     *
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param mixed $templatePath
     * @return self
     */
    public function setTemplatePath($templatePath): self
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}