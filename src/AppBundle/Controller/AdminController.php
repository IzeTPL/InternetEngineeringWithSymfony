<?php
/**
 * Created by PhpStorm.
 * User: Marian
 * Date: 11.12.2016
 * Time: 03:39
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Admin controller.
 *
 * @Route("panel/admin")
 */

class AdminController extends Controller {

	/**
	 * @Route("/", defaults={"count": 10, "page": 1}, name="multi_blog_panel_admin")
	 * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="multi_blog_panel_admin_paginated")
	 * @Method("GET")
	 * @Security("has_role('ROLE_ADMIN')")
	 * @Cache(smaxage="10")
	 */
	public function indexAction($page)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

		$paginator = $repository->paginate($page);
		$users = $repository->findAll();

		return $this->render('panel/admin/index.html.twig', array(
			'users' => $users,
			'paginator' => $paginator,
		));
	}

	/**
	 * @Route("/{id}/promote", name="user_promote")
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 * @Method({"GET", "POST"})
	 */
	public function promoteAction($id){

		$em=$this->getDoctrine()->getManager();
		$user=$em->getRepository('AppBundle:User')->find($id);
		$user->addRole('ROLE_ADMIN');
		$em->persist($user);
		$em->flush();

		return $this->redirectToRoute('multi_blog_panel_admin');

	}

	/**
	 * @Route("/{id}/downgrade", name="user_downgrade")
	 * @Security("has_role('ROLE_SUPER_ADMIN')")
	 * @Method({"GET", "POST"})
	 */
	public function downgradeAction($id){

		$em=$this->getDoctrine()->getManager();
		$user=$em->getRepository('AppBundle:User')->find($id);
		$user->removeRole('ROLE_ADMIN');
		$em->persist($user);
		$em->flush();

		return $this->redirectToRoute('multi_blog_panel_admin');

	}

	/**
	 * @Route("/{id}/delete", name="user_delete")
	 * @Security("has_role('ROLE_ADMIN')")
	 * @Method("DELETE")
	 */
	public function deleteAction(Request $request, User $user)
	{

		if ($user->hasRole('ROLE_SUPER_ADMIN')) {

			throw new AccessDeniedException();

		}

		$form = $this->createDeleteForm($user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($user);
			$em->flush($user);
		}

		return $this->redirectToRoute('multi_blog_panel_admin');
	}

	/**
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(User $user)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
			->setMethod('DELETE')
			->getForm()
			;
	}

	public function createDeleteFormAction(User $user)
	{

		$form = $this->createDeleteForm($user);

		return $this->render('panel/admin/_delete.html.twig', [
			'form' => $form->createView(),
		]);
	}

}