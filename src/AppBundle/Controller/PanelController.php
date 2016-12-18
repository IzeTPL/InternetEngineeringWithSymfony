<?php
/**
 * Created by PhpStorm.
 * User: Marian
 * Date: 11.12.2016
 * Time: 01:41
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Panel controller.
 *
 * @Route("panel")
 */
class PanelController extends Controller {
	/**
	 * @Route("/", defaults={"count": 10, "page": 1}, name="multi_blog_panel")
	 * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="multi_blog_panel_paginated")
	 * @Method("GET")
	 * @Security("has_role('ROLE_USER')")
	 * @Cache(smaxage="10")
	 */
	public function indexAction($page)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');

		$user = $this->container->get('security.token_storage')->getToken()->getUsername();
		$paginator = $repository->paginateAuthor($page, $user);
		$posts = $repository->queryAuthor($user)->setMaxResults(10)->getResult();

		return $this->render('panel/index.html.twig', array(
			'posts' => $posts,
			'paginator' => $paginator,
		));
	}
}