<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoFormType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoController extends AbstractController
{
    protected $todoRepository;
    protected $em;

    public function __construct(TodoRepository $todoRepository, EntityManagerInterface $em)
    {
        $this->todoRepository = $todoRepository;
        $this->em = $em;
    }

    /**
     * displays the home page of the todo list
     * @return Response
     * 
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $todos = $this->todoRepository->findAll();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * show the details of a todo
     * @param integer $id
     * @return void
     * 
     * @Route("todo/{id}", name="show_todo")
     */
    public function showTodo(int $id)
    {
        $todo = $this->todoRepository->find($id);
        return $this->render('todo/details.html.twig', [
            'todo' => $todo,
        ]);
    }

    /**
     * Create a todo
     * @param Request $request
     * @return void
     * 
     * @Route("create", name="todo_create")
     */
    public function create(Request $request)
    {

        $todo = new Todo();
        $form = $this->createForm(TodoFormType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todo->setState("Todo");
            $this->em->persist($todo);
            $this->em->flush();

            $this->addFlash('success', 'La Todo a bien été créée !');
            return $this->redirectToRoute('index');
        }

        return $this->render('todo/create.html.twig', [
            'formView' => $form->createView(),
        ]);
    }

    /**
     * change the state of the todo
     * @param Request $request
     * @param integer $id
     * @return Response
     * 
     * @Route("edit/state/{id}", name="todo_state_edit", methods={"POST"})
     */
    public function editState(Request $request, int $id): Response
    {
        $todo = $this->todoRepository->find($id);

        if ($request->isXmlHttpRequest()) {
            $todo->setState($request->request->get('state'));
            $this->todoRepository->add($todo);
            $json['response'] = 'success';
        } else {
            $json['response'] = "La requête n'a pas aboutie";
        }
        return new JsonResponse($json);
    }
}
