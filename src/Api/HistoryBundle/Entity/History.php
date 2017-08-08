<?php

namespace Api\HistoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="wefaves_history")
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $url;

    /**
     * Many History has One User
     * @ORM\ManyToOne(targetEntity="Api\UserBundle\Entity\User", inversedBy="histories")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="float", name="last_visit")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $lastVisit;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $typedCount;

    /**
     * @ORM\Column(type="integer")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $visitCount;

    /**
     * @ORM\Column(type="datetime", name="updated_at")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @Serializer\Groups({"GET_HISTORIES"})
     */
    protected $createdAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return History
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
     * @return History
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
     * Set lastVisit
     *
     * @param float $lastVisit
     *
     * @return History
     */
    public function setLastVisit($lastVisit)
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }

    /**
     * Get lastVisit
     *
     * @return float
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * Set typedCount
     *
     * @param integer $typedCount
     *
     * @return History
     */
    public function setTypedCount($typedCount)
    {
        $this->typedCount = $typedCount;

        return $this;
    }

    /**
     * Get typedCount
     *
     * @return integer
     */
    public function getTypedCount()
    {
        return $this->typedCount;
    }

    /**
     * Set visitCount
     *
     * @param integer $visitCount
     *
     * @return History
     */
    public function setVisitCount($visitCount)
    {
        $this->visitCount = $visitCount;

        return $this;
    }

    /**
     * Get visitCount
     *
     * @return integer
     */
    public function getVisitCount()
    {
        return $this->visitCount;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return History
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
     * @return History
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

    /**
     * Set user
     *
     * @param \Api\UserBundle\Entity\User $user
     *
     * @return History
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
}
