<?php

namespace Api\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit = 15, $offset = 1)
    {
        if (0 == $limit || 0 == $offset) {
            throw new \LogicException('$limit & $offset must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
        //$offset = (int) $offset;
        $currentPage = ceil( ($offset + 1) / $limit );
       // var_dump($currentPage); die;
        $pager->setMaxPerPage((int) $limit);
        $pager->setCurrentPage($currentPage);
        //$pager->setMaxPerPage((int) $limit);

       //var_dump($pager->getAdapter()->getQuery()->execute());

        return $pager;
    }
}