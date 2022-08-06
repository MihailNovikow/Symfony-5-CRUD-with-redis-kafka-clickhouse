<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/UserController.php',
        // ]);

        return $this->render('index.html.twig');
    }

    /**
     * @Route("/all", name="all")
     */
    public function all(Request $request, PaginatorInterface $paginator): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([],['id'=>'DESC']);

        $paginatedUsers = $paginator->paginate($users, $request->query->getInt('page', 1), 5);

        return $this->render('all.html.twig', ['Users' => $paginatedUsers]);
    }

    /**
     * @Route("/create", name="create-User", methods={"POST"})
     */
    public function create(Request $request) {

        $name = $request->request->get('User');

        $status = 0;

      //  $author = 'johndoe@name.com';

        $objectManager = $this->getDoctrine()->getManager();

        $lastUser = $objectManager->getRepository(User::class)->findOneBy([], ['id' => 'desc']);

        $lastId = $lastUser->getId();

        $newId = $lastId + 1;

        $User = new User;

        $User->setId($newId);

        $User->setName($name);

    //    $User->setStatus($status);

     //   $User->setAuthor($author);

        $objectManager->persist($User);

        $objectManager->flush();

        $this->addFlash('success', 'You have created a new User!');

        return $this->redirectToRoute('all');

    }

    /**
     * @Route("/updateStatus/{id}", name="update-status")
     */
    public function updateUserStatus($id) {

        $objectManager = $this->getDoctrine()->getManager();

        $User = $objectManager->getRepository(User::class)->find($id);

        $User->setStatus(!$User->getStatus());

        $objectManager->flush();

        $this->addFlash('info', 'You have updated a User!');

        return $this->redirectToRoute('all');

    }

    /**
     * @Route("/deleteUser/{id}", name="delete-User")
     */
    public function delete(User $id) {

        $objectManager = $this->getDoctrine()->getManager();

        $objectManager->remove($id);

        $objectManager->flush();

        $this->addFlash('danger', 'You have deleted a User!');

        return $this->redirectToRoute('all');

    }

}
