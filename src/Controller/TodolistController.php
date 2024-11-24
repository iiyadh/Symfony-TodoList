<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class TodolistController extends AbstractController
{
    #[Route('/todolist', name: 'app_todolist')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tasks = $entityManager->getRepository(Task::class)->findAll();

        return $this->render('todolist/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }


    #[Route('/task/add', name: 'add_task', methods: ['POST'])]
    public function addTask(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $description = $request->request->get('task');
        if ($description) {
            $task = new Task();
            $task->setDescription($description);
            $task->setState(false);
            $entityManager->persist($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_todolist');
    }

    #[Route('/task/toggle/{id}', name: 'toggle_task', methods: ['POST'])]
    public function toggleTask(Task $task, EntityManagerInterface $entityManager): RedirectResponse
    {
        $task->setState(!$task->isState());
        $entityManager->flush();

        return $this->redirectToRoute('app_todolist');
    }

    #[Route('/tasks/delete-completed', name: 'delete_completed', methods: ['POST'])]
    public function deleteCompleted(EntityManagerInterface $entityManager): RedirectResponse
    {
        $tasks = $entityManager->getRepository(Task::class)->findBy(['state' => true]);
        foreach ($tasks as $task) {
            $entityManager->remove($task);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_todolist');
    }
}
