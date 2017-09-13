<?php

namespace Api\UserBundle\Repository;

class UserRepository extends AbstractRepository
{
    public function search($term, $order = 'asc', $limit = 20, $offset = 0)
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

        //var_dump($qb->getQuery()->execute());

        //$qb->getQuery();

        return $this->paginate($qb, $limit, $offset);
    }
}