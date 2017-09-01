<?php

namespace Api\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity
 * @ORM\Table(name="wefaves_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"GET_USERS", "GET_USER", "POST_USER", "GET_AUTHENTICATED_USER"})
     */
    protected $id;

    /**
     * @Serializer\Groups({"GET_USERS", "GET_USER", "POST_USER", "GET_AUTHENTICATED_USER"})
     */
    protected $email;

    /**
     * One User has Many History.
     * @ORM\OneToMany(targetEntity="Api\HistoryBundle\Entity\History", mappedBy="user")
     */
    private $histories;

    /**
     * One User has Many History.
     * @ORM\OneToMany(targetEntity="Api\HistoryBundle\Entity\History", mappedBy="user")
     */
    protected $bookmarks;

    /**
     * One User has Many Bookmark Folder.
     * @ORM\OneToMany(targetEntity="Api\BookmarkBundle\Entity\BookmarkFolder", mappedBy="user")
     */
    private $bookmarkFolder;

    public function __construct()
    {
        parent::__construct();

        $this->histories = new ArrayCollection();
        $this->bookmarks = new ArrayCollection();
        $this->bookmarkFolder = new ArrayCollection();
    }

    /**
     * Add history
     *
     * @param \Api\HistoryBundle\Entity\History $history
     *
     * @return User
     */
    public function addHistory(\Api\HistoryBundle\Entity\History $history)
    {
        $this->histories[] = $history;

        $history->setUser($this);

        return $this;
    }

    /**
     * Remove history
     *
     * @param \Api\HistoryBundle\Entity\History $history
     */
    public function removeHistory(\Api\HistoryBundle\Entity\History $history)
    {
        $this->histories->removeElement($history);
    }

    /**
     * Get histories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * Add bookmark
     *
     * @param \Api\BookmarkBundle\Entity\Bookmark $bookmark
     *
     * @return User
     */
    public function addBookmark(\Api\BookmarkBundle\Entity\Bookmark $bookmark)
    {
        $this->bookmarks[] = $bookmark;

        $bookmark->setUser($this);

        return $this;
    }

    /**
     * Remove history
     *
     * @param \Api\BookmarkBundle\Entity\Bookmark $bookmark
     */
    public function removeBookmark(\Api\BookmarkBundle\Entity\Bookmark $bookmark)
    {
        $this->bookmarks->removeElement($bookmark);
    }

    /**
     * Get histories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookmarks()
    {
        return $this->bookmarks;
    }

    /**
     * Add bookmark folder
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     *
     * @return User
     */
    public function addBookmarkFolder(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder)
    {
        $this->bookmarkFolder[] = $bookmarkFolder;

        $bookmarkFolder->setUser($this);

        return $this;
    }

    /**
     * Remove bookmark folder
     *
     * @param \Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder
     */
    public function removeBookmarkFolder(\Api\BookmarkBundle\Entity\BookmarkFolder $bookmarkFolder)
    {
        $this->bookmarkFolder->removeElement($bookmarkFolder);
    }

    /**
     * Get bookmark folders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookmarkFolder()
    {
        return $this->bookmarkFolder;
    }

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

}