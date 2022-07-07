<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Entity\Question;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categorie = $entityManager
            ->getRepository(Categorie::class)
            ->findAll();



        return $this->render('home/index.html.twig', [
            'categories' => $categorie,


        ]);
    }
}
?>