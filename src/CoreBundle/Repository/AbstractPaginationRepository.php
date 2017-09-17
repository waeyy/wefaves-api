<?php

namespace CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractPaginationRepository extends EntityRepository
{
    protected function paginate(QueryBuilder $qb, $limit = 15, $offset = 1)
    {
        if (0 == $limit || 0 == $offset) {
            throw new \LogicException('$limit & $offset must be greater than 0.');
        }

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb));

        $currentPage = ceil( ($offset + 1) / $limit );

        $pager->setMaxPerPage((int) $limit);
        $pager->setCurrentPage($currentPage);
        
        return $pager;
    }
}