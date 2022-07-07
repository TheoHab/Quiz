<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="app_question_index", methods={"GET"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $t = explode('/', $request);
        $z = $t[2];
        preg_match_all('!\d+!', $z, $match);
        $matchs = implode("", $match[0]);
        $questions = $entityManager
            ->getRepository(Question::class)
            ->findBy(
                ['id' => $matchs]
            );
        $reponse = $entityManager
            ->getRepository(Reponse::class)
            ->findBy(
                ['idQuestion' => $questions],
            );

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
            'reponses' => $reponse,
            'idCategorie' => $matchs,
        ]);
    }

    /**
     * @Route("/new", name="app_question_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/new.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_question_show", methods={"GET", "POST"})
     */
    public function show(Question $question, EntityManagerInterface $entityManager, Request $request): Response
    {
        $t = explode('/', $request);
        $z = $t[2];
        preg_match_all('!\d+!', $z, $match);
        $matchs = implode("", $match[0]);


        $isQuestion = $entityManager
            ->getRepository(Question::class)
            ->findBy(
                ['id' => $matchs],
                null,
                1
            );

        $NextQuestion = $entityManager
            ->getRepository(Question::class)
            ->findBy(
                ['id' => $matchs + 1],
                null,
                1
            );

        $idCategorieUn = $isQuestion[0]->getidCategorie();
        $idCategorieDeux = $NextQuestion[0]->getidCategorie();

        $reponse = $entityManager
            ->getRepository(Reponse::class)
            ->findBy(
                ['idQuestion' => $isQuestion],

            );

        return $this->render('question/show.html.twig', [
            "isQuestion" => $isQuestion,
            'reponses' => $reponse,
            "idCategorieUn" => $idCategorieUn,
            'idCategorieDeux' => $idCategorieDeux,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_question_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_question_delete", methods={"POST"})
     */
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $question->getId(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }
}
?>