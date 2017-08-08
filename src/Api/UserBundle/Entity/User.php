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

    public function __construct()
    {
        parent::__construct();

        $this->histories = new ArrayCollection();
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

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

}