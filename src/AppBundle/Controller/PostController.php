<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use FOS\UserBundle\FOSUserBundle;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Post controller.
 *
 * @Route("post")
 */
class PostController extends Controller
{
	/**
	 * @Route("/", defaults={"count": 10, "page": 1}, name="multi_blog_index")
	 * @Route("/page/{page}", requirements={"page": "[1-9]\d*"}, name="multi_blog_index_paginated")
	 * @Method("GET")
	 * @Cache(smaxage="10")
	 */
    public function indexAction($page)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');

	    $count = 10;
	    $next = $repository->getHighestId() - ( ($page - 1) * Post::NUM_ITEMS );
        $posts = $repository->findLatest($count, $next)->getResult();
	    $this->get('session')->set('next', $next);
	    $this->get('session')->set('page', $page);
	    $this->get('session')->set('count', $count);

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new post entity.
     *
     * @Route("/new", name="post_new")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

	    $user = $this->getUser();

	    if (!$user->hasRole('ROLE_USER') ) {
		    throw new AccessDeniedException();
	    }

        $post = new Post();
	    $post->setAuthor($this->getUser()->getUsername());
        $form = $this->createForm('AppBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $post->setSlug($this->get('slugger')->slugify($post->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush($post);

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/{slug}", name="post_show", options = { "expose" = true } )
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
        ));
    }

	/**
	 * @return Response
	 */
    public function showMoreAction() {

	    $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
	    $count = $this->get('session')->get('count');
	    $next = $this->get('session')->get('next') - $count;
	    $posts = $em->findLatest($count, $next)->getResult();
	    $this->get('session')->set('next', $next);

	    $template =  $this->renderView('post/_article.html.twig', array(
		    'posts' => $posts,
	    ));

	    $response = new Response(json_encode( array("template" => $template) ) );
	    $response->headers->set('Content-Type', 'application/json');

	    return $response;

    }

	/**
	 * @return Response
	 */
	public function showPaginationAction() {

		$page = $next = $this->get('session')->get('page');
		$em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
		$paginator = $em->paginate($page);

		$template =  $this->renderView('post/_pagination.html.twig', array(
			'paginator' => $paginator,
		));

		$response = new Response(json_encode( array("template" => $template) ) );
		$response->headers->set('Content-Type', 'application/json');

		return $response;

	}

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Security("has_role('ROLE_USER')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post) {

	    $user = $this->getUser();

	    if ($post->getAuthor() != $user && (!$user->hasRole('ROLE_ADMIN') && !$user->isSuperAdmin())) {
		    throw new AccessDeniedException();
	    }

	    $deleteForm = $this->createDeleteForm($post);
	    $editForm = $this->createForm('AppBundle\Form\PostType', $post);
	    $editForm->handleRequest($request);

	    if ($editForm->isSubmitted() && $editForm->isValid()) {
		    $this->getDoctrine()->getManager()->flush();

		    return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
	    }

	    return $this->render('post/edit.html.twig', array(
		    'post' => $post,
		    'edit_form' => $editForm->createView(),
		    'delete_form' => $deleteForm->createView(),
	    ));

    }

    /**
     * Deletes a post entity.
     *
     * @Route("/{id}", name="post_delete")
     * @Security("has_role('ROLE_USER')")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Post $post)
    {

	    $user = $this->getUser();

	    if ($post->getAuthor() != $user && (!$user->hasRole('ROLE_ADMIN') && !$user->isSuperAdmin()) ) {
		    throw new AccessDeniedException();
	    }

        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush($post);
        }

        return $this->redirectToRoute('multi_blog_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

	public function createDeleteFormAction(Post $post)
	{

		$form = $this->createDeleteForm($post);

		return $this->render('post/_delete.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
