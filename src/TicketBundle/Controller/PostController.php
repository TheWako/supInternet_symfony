<?php

namespace TicketBundle\Controller;

use TicketBundle\Entity\Post;
use TicketBundle\Entity\Comment;
use TicketBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Post controller.
 *
 * @Route("post")
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        
        if($user->getRoles()[0] == "ROLE_ADMIN"){
            $posts = $em->getRepository('TicketBundle:Post')->findAll();
        }else{
            $connection = $em->getConnection();
            $statement = $connection->prepare("SELECT * FROM comment JOIN post ON post_id = post.id JOIN user ON user_id = user.id WHERE user_id =".$user->getId());
            $statement->execute();
            $posts = $statement->fetchAll();
        }

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = $this->getUser();
        $post = new Post();
        $form = $this->createForm('TicketBundle\Form\PostType', $post);
        $form->handleRequest($request);

        $comment = new Comment();
        $form2 = $this->createForm('TicketBundle\Form\CommentType', $comment);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $em->persist($post);
            $em->flush($post);
            $em->persist($comment);
            $em->flush($comment);
            $statement = $connection->prepare("UPDATE comment SET post_id = ".$post->getId().", user_id = ".$user->getId()." WHERE id =".$comment->getId().";");
            $statement->execute();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
            'form2' => $form2->createView()
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     * @Route("/{id}", name="post_show")
     * @Method({"GET", "POST"})
     */
    public function showAction(Post $post, Request $request)
    {
        $deleteForm = $this->createDeleteForm($post);

        $comment = new Comment();
        $form = $this->createForm('TicketBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $em->persist($comment);
            $em->flush($comment);
            $statement = $connection->prepare("UPDATE comment SET post_id = ".$post->getId()." WHERE id =".$comment->getId().";");
            $statement->execute();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM comment WHERE post_id =".$post->getId());
        $statement->execute();
        $result = $statement->fetchAll();

        return $this->render('post/show.html.twig', array(
            'post' => $post,
            'delete_form' => $deleteForm->createView(),
            'form' => $form->createView(),
            'comments' => $result
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('TicketBundle\Form\PostType', $post);
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
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Post $post)
    {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $connection = $em->getConnection();
            $statement = $connection->prepare("DELETE FROM `comment` WHERE post_id =".$post->getId());
            $statement->execute();
            $em->remove($post);
            $em->flush($post);
        }

        return $this->redirectToRoute('post_index');
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
}
