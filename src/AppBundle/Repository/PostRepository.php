<?php

namespace AppBundle\Repository;

/**
 * PostRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findLatestNews($limit)
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 2)
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPaginationBlog()
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 2)
            ->getQuery();
    }

    public function findRelatedBlog($limited, $tag)
    {
        return $this->createQueryBuilder('p')
            ->where('p.tag = :tag')
            ->setParameter('tag', $tag)
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limited)
            ->getQuery()
            ->getResult();
    }

    public function findCategoryBlog($data)
    {
        return $this->createQueryBuilder('p')
            ->where('p.categoryId = :category')
            ->andwhere('p.status = :status')
            ->setParameter('category', $data)
            ->setParameter('status', 2)
            ->getQuery();
    }
}
