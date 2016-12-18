<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post
{

	const NUM_ITEMS = 100;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $slug;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank(message="post.blank_summary")
	 */
	private $summary;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 * @Assert\NotBlank(message="post.blank_content")
	 * @Assert\Length(min = "10", minMessage = "post.too_short_content")
	 */
	private $content;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	private $author;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 * @Assert\DateTime()
	 */
	private $publishedAt;

	public function getId()
	{
		return $this->id;
	}

	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}

	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * Is the given User the author of this Post?
	 */
	public function isAuthor(User $user)
	{
		return $user->getUsername() === $this->getAuthor();
	}

	public function getPublishedAt()
	{
		return $this->publishedAt;
	}

	public function setPublishedAt(\DateTime $publishedAt)
	{
		$this->publishedAt = $publishedAt;
	}

	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * @param string $summary
	 */
	public function setSummary($summary)
	{
		$this->summary = $summary;
	}
}
