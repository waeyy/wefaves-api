<?php

namespace Api\HistoryBundle\Repository;

use CoreBundle\Repository\AbstractPaginationRepository;

class HistoryRepository extends AbstractPaginationRepository
{
    public function filter($term, $order = 'asc', $limit = 20, $offset = 0, $userId)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->where('a.user = ?1')
            ->setParameter(1, $userId)
            ->orderBy('a.id', $order)
        ;

        if ($term) {
            $qb
                ->where('a.title LIKE ?2')
                ->setParameter(2, '%'.$term.'%')
            ;
        }

        //var_dump($qb->getQuery()); die;

        return $this->paginate($qb, $limit, $offset);
    }
}