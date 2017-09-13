<?php

namespace Api\UserBundle\Representation;

use Pagerfanta\Pagerfanta;
use JMS\Serializer\Annotation as Serializer;

class Users
{
    /**
     * @Serializer\Type("array<Api\UserBundle\Entity\User>")
     */
    public $data;
    public $meta;

    public function __construct(Pagerfanta $data)
    {
        //var_dump($data->getCurrentPageOffsetStart());

        $this->data = $data;

        $this->addMeta('limit', $data->getMaxPerPage());
        $this->addMeta('current_items', count($data->getCurrentPageResults()));
        $this->addMeta('total_items', $data->getNbResults());
        $this->addMeta('offset', $data->getCurrentPageOffsetStart());
        $this->addMeta('paginate', $data->haveToPaginate());
        $this->addMeta('previous', $data->hasPreviousPage());
        $this->addMeta('currentPage', $data->getCurrentPage());
        //$this->addMeta('previousPage', $data->getPreviousPage());
        $this->addMeta('next', $data->hasNextPage());
        //$this->addMeta('nextPage', $data->getNextPage());

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