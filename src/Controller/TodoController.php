<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoController extends AbstractController
{
    protected $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
    }

    /**
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
     *
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

    // /**
    //  * @Route("create")
    //  */
    // public function create()
    // {



    //     return $this->render('todo/create.html.twig');
    // }

    /**
     * Undocumented function
     *
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
            // $todo->setTitle($request->request->get('title'));
            $this->todoRepository->add($todo);
            $json['response'] = 'success';
        } else {
            $json['response'] = "La requête n'a pas aboutie";
        }
        return new JsonResponse($json);
    }
}
