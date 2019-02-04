<?php

namespace CequensBundle\Controller;

use CequensBundle\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class UserController extends Controller
{
    protected $userService;

    /**
     * UserController constructor.
     * @param $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @Route("/",name="settings_user_list_all")
     */
    public function indexAction()
    {
        $users = $this->userService->getUsers();
        return $this->render('@Cequens/Admin/pages/User/list_user.html.twig', array('users' => $users));
    }

    /**
     * @Route("/new",name="settings_user_show_create")
     */
    public function showCreateAction()
    {
        $users = $this->userService->getUsers();
        return $this->render('@Cequens/Admin/pages/User/create_user.html.twig', array('users' => $users));
    }
}
