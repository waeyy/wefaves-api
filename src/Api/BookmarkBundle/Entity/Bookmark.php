<?php

namespace Api\BookmarkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bookmark
 *
 * @ORM\Entity(repositoryClass="Api\BookmarkBundle\Repository\BookmarkRepository")
 * @ORM\Table(name="wefaves_bookmark")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Bookmark
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * Many Bookmark has One User
     * @ORM\ManyToOne(targetEntity="Api\UserBundle\Entity\User", inversedBy="bookmarks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Many Boookmarks folder has One Bookmark Folder
     * @ORM\ManyToOne(targetEntity="Api\BookmarkBundle\Entity\BookmarkFolder", inversedBy="bookmarks")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $bookmarkFolder;

    /**
     * @ORM\Column(name="date_added", type="bigint")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $dateAdded;

    /**
     * @ORM\Column(name="item_id", type="integer")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $itemId;

    /**
     * @ORM\Column(name="index_pos", type="integer")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $indexPos;

    /**
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $parentId;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $title;

    /**
     * @ORM\Column(name="url", type="string", length=255)
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Url(message = "The url '{{ value }}' is not a valid url.")
     */
    protected $url;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     */
    protected $createdAt;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime());
        $this->setCreatedAt(new \DateTime());
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
     * Set user
     *
     * @param \Api\UserBundle\Entity\User $user
     *
     * @return Bookmark
     */
    public function setUser(\Api\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Api\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set bookmark folder
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     *
     * @return Bookmark
     */
    public function setBookmarkFolder(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder = null)
    {
        $this->bookmarkFolder = $bookmarkFolder;

        return $this;
    }

    /**
     * Get bookmark folder
     *
     * @return \Api\BookmarkBundle\Entity\BookmarkFolder
     */
    public function getBookmarkFolder()
    {
        return $this->bookmarkFolder;
    }

    /**
     * Set dateAdded
     *
     * @param integer $dateAdded
     *
     * @return Bookmark
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return int
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return Bookmark
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set indexPos
     *
     * @param integer $indexPos
     *
     * @return Bookmark
     */
    public function setIndexPos($indexPos)
    {
        $this->indexPos = $indexPos;

        return $this;
    }

    /**
     * Get indexPos
     *
     * @return int
     */
    public function getIndexPos()
    {
        return $this->indexPos;
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     *
     * @return Bookmark
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return BookmarkFolder
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return BookmarkFolder
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Bookmark
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Bookmark
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

