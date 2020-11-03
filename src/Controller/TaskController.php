<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list_not_done")
     */
    public function listNotDoneAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findBy(['isDone' => 0])]);
    }

    /**
     * @Route("/tasks/done", name="task_list_done")
     */
    public function listDoneAction()
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->getDoctrine()->getRepository('App:Task')->findBy(['isDone' => 1])]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // is user is authenticated, then user is added to task, else user will be null
            $task->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list_not_done');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        // if author of task is anonymous, then check if user has admin rights
        if (null === $task->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        // check if user is the author of the task, and author of the task is not anonymous
        if (($this->getUser() !== $task->getUser()) && (null !== $task->getUser())) {
            $this->addFlash('error', sprintf('Vous n\'êtes pas l\'auteur(e) de la tâche %s.', $task->getTitle()));

            return $this->redirectToRoute('task_list_not_done');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list_not_done');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été modifié.', $task->getTitle()));

        return $this->redirectToRoute('task_list_not_done');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {
        // if author of task is anonymous, then check if user has admin rights
        if (null === $task->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        // check if user is the author of the task, and author of the task is not anonymous
        if (($this->getUser() !== $task->getUser()) && (null !== $task->getUser())) {
            $this->addFlash('error', sprintf('Vous n\'êtes pas l\'auteur(e) de la tâche %s.', $task->getTitle()));

            return $this->redirectToRoute('task_list_not_done');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list_not_done');
    }
}
