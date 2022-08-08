<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

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

        return $this->render('all.html.twig', ['users' => $paginatedUsers]);
    }

    /**
     * @Route("/create", name="create-user", methods={"POST"})
     */
    public function create(Request $request) {

        $name = $request->request->get('user');

        $status = 0;

       // $author = 'johndoe@name.com';

        $objectManager = $this->getDoctrine()->getManager();

        $lastUser = $objectManager->getRepository(User::class)->findOneBy([], ['id' => 'desc']);

        $lastId = $lastUser->getId();

        $newId = $lastId + 1;

        $user = new User;

        $user->setId($newId);

        $user->setName($name);

        $user->setStatus($status);

        $objectManager->persist($user);

        $objectManager->flush();

        $this->addFlash('success', 'You have created a new User!');

        return $this->redirectToRoute('all');

        //cashing



        $cache = new RedisAdapter(

    // the object that stores a valid connection to your Redis system
        $redisConnection,

    // the string prefixed to the keys of the items stored in this cache
        $namespace = '',

    // the default lifetime (in seconds) for cache items that do not define their
    // own lifetime, with a value 0 causing items to be stored indefinitely (i.e.
    // until RedisAdapter::clear() is invoked or the server(s) are purged)
        $defaultLifetime = 60
);

        $client = RedisAdapter::createConnection(

    // provide a string dsn
        'redis://localhost:6379',

    // associative array of configuration options
    [
        'lazy' => false,
        'persistent' => 0,
        'persistent_id' => null,
        'tcp_keepalive' => 0,
        'timeout' => 30,
        'read_timeout' => 0,
        'retry_interval' => 0,
    ]

);
        $client = RedisAdapter::createConnection('redis://localhost');
        $cache = new RedisTagAwareAdapter($client);

    }

    /**
     * @Route("/updateStatus/{id}", name="update-status")
     */
    public function updateUserStatus($id) {

        $objectManager = $this->getDoctrine()->getManager();

        $user = $objectManager->getRepository(User::class)->find($id);

        $user->setStatus(!$user->getStatus());

        $objectManager->flush();

        $this->addFlash('info', 'You have updated a User!');

        return $this->redirectToRoute('all');

    }

    /**
     * @Route("/delete-user/{id}", name="delete-user")
     */
    public function delete(User $id) {

        $objectManager = $this->getDoctrine()->getManager();

        $objectManager->remove($id);

        $objectManager->flush();

        $this->addFlash('danger', 'You have deleted a User!');

        return $this->redirectToRoute('all');

    }

}