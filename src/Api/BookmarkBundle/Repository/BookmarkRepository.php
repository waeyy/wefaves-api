<?php

namespace Api\BookmarkBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookmarkRepository extends EntityRepository {

    public function getListBookmarksFolders($user, $order = "asc") {

        $qb = $this
            ->createQueryBuilder('bf')
            ->select('bf')
            ->orderBy('bf.id', $order)
            ->where('bf.user = ?1')
            ->setParameter(1, $user)
            ->andWhere('bf.bookmarkFolderParent is NULL')
        ;

        return ($qb->getQuery()->execute());
    }
}