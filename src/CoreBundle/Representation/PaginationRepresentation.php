<?php

namespace CoreBundle\Representation;

use Pagerfanta\Pagerfanta;
use JMS\Serializer\Annotation as Serializer;

class PaginationRepresentation
{
    public $data;
    public $meta;

    public function __construct(Pagerfanta $data)
    {
        $this->data = $data;

        $this->addMeta('total_items', $data->getNbResults());
        $this->addMeta('total_pages', $data->getNbPages());
        $this->addMeta('current_page', $data->getCurrentPage());

        if ( !($data->getCurrentPageOffsetStart() < $data->getMaxPerPage()) )
            $this->addMeta('previous_page', $data->getPreviousPage());
        else
            $this->addMeta('previous_page', null);

        if ( !($data->getCurrentPageOffsetStart() >= $data->getNbResults()) && ($data->getNbPages() > 1) )
            $this->addMeta('next_page', $data->getNextPage());
        else
            $this->addMeta('next_page', null);
    }

    public function addMeta($name, $value)
    {
        if (isset($this->meta[$name])) {
            throw new \LogicException(sprintf('This meta already exists. You are trying to override this meta, use the setMeta method instead for the %s meta.', $name));
        }

        $this->setMeta($name, $value);
    }

    public function setMeta($name, $value)
    {
        $this->meta[$name] = $value;
    }
}