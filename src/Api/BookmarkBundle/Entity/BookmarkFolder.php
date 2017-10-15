<?php

namespace Api\BookmarkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BookmarkFolder
 *
 * @ORM\Entity(repositoryClass="Api\BookmarkBundle\Repository\BookmarkRepository")
 * @ORM\Table(name="wefaves_bookmark_folder")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class BookmarkFolder
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * Many Boookmarks folder has One User
     * @ORM\ManyToOne(targetEntity="Api\UserBundle\Entity\User", inversedBy="bookmarkFolder")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * One Bookmark folder has Many Bookmarks.
     * @ORM\OneToMany(targetEntity="Api\BookmarkBundle\Entity\Bookmark", mappedBy="bookmarkFolder", cascade={"persist", "remove"})
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $bookmarks;

    /**
     * One Bookmark folder has Many Bookmarks.
     * @ORM\OneToMany(targetEntity="Api\BookmarkBundle\Entity\BookmarkFolder", mappedBy="bookmarkFolderParent", cascade={"persist", "remove"})
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $bookmarkFolderChild;

    /**
     * Many Boookmarks folder Child has One Bookmark Folder
     * @ORM\ManyToOne(targetEntity="Api\BookmarkBundle\Entity\BookmarkFolder", inversedBy="bookmarkFolderChild")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $bookmarkFolderParent;

    /**
     * @var int
     *
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
     * @var int
     *
     * @ORM\Column(name="date_group_modified", type="bigint")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $dateGroupModified;

    /**
     * @var int
     *
     * @ORM\Column(name="item_id", type="string")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    protected $itemId;

    /**
     * @var int
     *
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
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     * @Serializer\Expose
     */
    protected $parentId;

    /**
     * @var string
     *
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     */
    protected $updatedAt;

    /**
      @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @Serializer\Groups({"GET_BOOKMARKS_FOLDERS"})
     */
    protected $createdAt;

    public function __construct()
    {
        $this->setUpdatedAt(new \DateTime());
        $this->setCreatedAt(new \DateTime());

        $this->bookmarks = new ArrayCollection();
        $this->bookmarksFolderChild = new ArrayCollection();
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
     * @return BookmarkFolder
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
 * Add bookmark
 *
 * @param \Api\BookmarkBundle\Entity\Bookmark $bookmark
 *
 * @return Bookmark
 */
    public function addBookmark(\Api\BookmarkBundle\Entity\Bookmark $bookmark)
    {
        $this->bookmarks[] = $bookmark;

        $bookmark->setBookmarkFolder($this);

        return $this;
    }

    /**
     * Remove bookmark
     *
     * @param \Api\BookmarkBundle\Entity\Bookmark $bookmark
     */
    public function removeBookmark(\Api\BookmarkBundle\Entity\Bookmark $bookmark)
    {
        $this->bookmarks->removeElement($bookmark);
    }

    /**
     * Get bookmarks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookmark()
    {
        return $this->bookmarks;
    }

    /**
     * Add bookmark
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     *
     * @return Bookmark
     */
    public function addBookmarkFolderChild(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder)
    {
        $this->bookmarkFolderChild[] = $bookmarkFolder;

        $bookmarkFolder->setBookmarkFolderParent($this);

        return $this;
    }

    /**
     * Remove bookmark
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     */
    public function removeBookmarkFolderChild(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder)
    {
        $this->bookmarkFolderChild->removeElement($bookmarkFolder);
    }

    /**
     * Get bookmarks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookmarkFolderChild()
    {
        return $this->bookmarkFolderChild;
    }

    /**
     * Set user
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     *
     * @return Bookmark
     */
    public function setBookmarkFolderParent(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder = null)
    {
        $this->bookmarkFolderParent = $bookmarkFolder;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Api\BookmarkBundle\Entity\BookmarkFolder
     */
    public function getBookmarkFolderParent()
    {
        return $this->bookmarkFolderParent;
    }

    /**
     * Set dateAdded
     *
     * @param integer $dateAdded
     *
     * @return BookmarkFolder
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
     * Set dateGroupModified
     *
     * @param integer $dateGroupModified
     *
     * @return BookmarkFolder
     */
    public function setDateGroupModified($dateGroupModified)
    {
        $this->dateGroupModified = $dateGroupModified;

        return $this;
    }

    /**
     * Get dateGroupModified
     *
     * @return int
     */
    public function getDateGroupModified()
    {
        return $this->dateGroupModified;
    }

    /**
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return BookmarkFolder
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
     * @return BookmarkFolder
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
     * @return BookmarkFolder
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return BookmarkFolder
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
     * @return BookmarkFolder
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

