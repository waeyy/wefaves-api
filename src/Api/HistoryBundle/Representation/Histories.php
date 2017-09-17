<?php

namespace Api\HistoryBundle\Representation;

use CoreBundle\Representation\PaginationRepresentation;
use Pagerfanta\Pagerfanta;
use JMS\Serializer\Annotation as Serializer;

class Histories extends PaginationRepresentation
{
    /**
     * @Serializer\Type("array<Api\HistoryBundle\Entity\History>")
     */
    public $data;

    public function __construct(Pagerfanta $data)
    {
        parent::__construct($data);
    }
}