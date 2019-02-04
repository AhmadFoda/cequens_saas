<?php

namespace CequensBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class DefaultController extends Controller
{
    /**
     * @Route("/dashboard",name="admin_dashboard")
     */
    public function adminAction()
    {
        return $this->render('@Cequens/Admin/pages/dashboard.html.twig');
    }

	/**
	 * @Route("/",name="admin_index")
	 */
	public function indexAction()
	{
		$auth_checker = $this->get('security.authorization_checker');
		if($auth_checker->isGranted(['ROLE_ADMIN']))
		{
			return $this->redirectToRoute('admin_dashboard');
		}
		else
		{
			return $this->redirectToRoute('fos_user_security_login');
		}
		//
	}
}
