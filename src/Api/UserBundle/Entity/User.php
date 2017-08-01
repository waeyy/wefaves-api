<?php

namespace Api\UserBundle\Entity;

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
     * @Serializer\Groups({"GET_USERS", "GET_AUTHENTICATED_USER"})
     */
    protected $id;

    /**
     * @Serializer\Groups({"GET_USERS", "GET_AUTHENTICATED_USER"})
     */
    protected $username;

    /**
     * @Serializer\Groups({"GET_AUTHENTICATED_USER"})
     */
    protected $email;

    public function __construct()
    {
        parent::__construct();
    }
}