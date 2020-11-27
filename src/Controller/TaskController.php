<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list_not_done")
     */
    public function listNotDoneAction(
        TaskRepository $taskRepo,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $tasks = $taskRepo->findBy(['isDone' => 0]);
        $paginated = $paginator->paginate($tasks, $request->query->getInt('page', 1));
        $paginated->setTemplate('pagination/twitter_bootstrap_v4_pagination.html.twig');

        return $this->render('task/list.html.twig', ['tasks' => $paginated]);
    }

    /**
     * @Route("/tasks/done", name="task_list_done")
     */
    public function listDoneAction(TaskRepository $taskRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $tasks = $taskRepo->findBy(['isDone' => 1]);
        $paginated = $paginator->paginate($tasks, $request->query->getInt('page', 1));
        $paginated->setTemplate('pagination/twitter_bootstrap_v4_pagination.html.twig');

        return $this->render('task/list.html.twig', ['tasks' => $paginated]);
    }

    /**
     * @Route("/tasks/all", name="task_list_all")
     */
    public function listAllAction(TaskRepository $taskRepo, PaginatorInterface $paginator, Request $request): Response
    {
        $tasks = $taskRepo->findAll();
        $paginated = $paginator->paginate($tasks, $request->query->getInt('page', 1));
        $paginated->setTemplate('pagination/twitter_bootstrap_v4_pagination.html.twig');

        return $this->render('task/list.html.twig', ['tasks' => $paginated]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request): Response
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
    public function editAction(Task $task, Request $request): Response
    {
        // checks permissions calling TaskVoter
        if (!$this->isGranted('TASK_EDIT', $task)) {
            $this->addFlash(
                'error',
                sprintf('Vous n\'êtes pas administrateur, vous ne pouvez modifier une tâche anonyme, 
                ou vous n\'êtes pas l\'auteur(e) de la tâche')
            );

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
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task): Response
    {
        // checks permissions calling TaskVoter
        if (!$this->isGranted('TASK_DELETE', $task)) {
            $this->addFlash(
                'error',
                sprintf('Vous n\'êtes pas administrateur, vous ne pouvez modifier une tâche anonyme, 
                ou vous n\'êtes pas l\'auteur(e) de la tâche')
            );

            return $this->redirectToRoute('task_list_not_done');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list_not_done');
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task): Response
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', sprintf('Le statut de la tâche %s a bien été modifié.', $task->getTitle()));

        return $this->redirectToRoute('task_list_not_done');
    }
}
