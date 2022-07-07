<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Reponse;

/**
 * @Route("/categorie")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/", name="app_categorie_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="app_categorie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_categorie_show", methods={"GET"})
     */
    public function show(EntityManagerInterface $entityManager, Request $request): Response
    {
        $t = explode('/', $request);
        $z = $t[2];
        preg_match_all('!\d+!', $z, $match);
        $matchs = implode("", $match[0]);

        $isQuestion = $entityManager
            ->getRepository(Question::class)
            ->findBy(
                ['idCategorie' => $matchs],
                null,
                1
            );

        $NextQuestion = $entityManager
            ->getRepository(Question::class)
            ->findBy(
                ['idCategorie' => $matchs],
                null,
                1
            );

        $reponse = $entityManager
            ->getRepository(Reponse::class)
            ->findBy(
                ['idQuestion' => $isQuestion],

            );

        $idCategorieUn = $isQuestion[0]->getidCategorie();
        $idCategorieDeux = $NextQuestion[0]->getidCategorie();

        return $this->render('question/show.html.twig', [
            "isQuestion" => $isQuestion,
            'reponses' => $reponse,
            "idCategorieUn" => $idCategorieUn,
            'idCategorieDeux' => $idCategorieDeux,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_categorie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_categorie_delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
?>
