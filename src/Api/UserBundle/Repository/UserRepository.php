<?php

namespace Api\UserBundle\Repository;

use CoreBundle\Repository\AbstractPaginationRepository;

class UserRepository extends AbstractPaginationRepository
{
    public function filter($term, $order = 'asc', $limit = 20, $offset = 0)
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->select('a')
            ->orderBy('a.id', $order)
        ;

        if ($term) {
            $qb
                ->where('a.email LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
    }
}