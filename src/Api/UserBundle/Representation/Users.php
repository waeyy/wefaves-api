<?php

namespace Api\UserBundle\Representation;

use CoreBundle\Representation\PaginationRepresentation;
use Pagerfanta\Pagerfanta;
use JMS\Serializer\Annotation as Serializer;

class Users extends PaginationRepresentation
{
    /**
     * @Serializer\Type("array<Api\UserBundle\Entity\User>")
     */
    public $data;

    public function __construct(Pagerfanta $data)
    {
        parent::__construct($data);
    }
}