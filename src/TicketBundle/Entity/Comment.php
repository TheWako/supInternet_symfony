<?php

namespace TicketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TicketBundle\Entity\Post;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateComment", type="datetime")
     */
    private $dateComment;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->dateComment = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateComment
     *
     * @param \DateTime $dateComment
     *
     * @return Comment
     */
    public function setDateComment($dateComment)
    {
        $this->dateComment = $dateComment;

        return $this;
    }

    /**
     * Get dateComment
     *
     * @return \DateTime
     */
    public function getDateComment()
    {
        return $this->dateComment;
    }

    /**
     * Set post
     *
     * @param \TicketBundle\Entity\Post $post
     *
     * @return Comment
     */
    public function setPost(\TicketBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \TicketBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \TicketBundle\Entity\User $user
     *
     * @return Comment
     */
    public function setUser(\TicketBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TicketBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
