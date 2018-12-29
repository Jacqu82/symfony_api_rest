<?php

namespace AppBundle\Resource\Filtering\Role;

use AppBundle\Repository\RoleRepository;
use AppBundle\Resource\Filtering\ResourceFilterInterface;
use Doctrine\ORM\QueryBuilder;

class RoleResourceFilter implements ResourceFilterInterface
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param RoleFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        return $this->getQuery($filter)
            ->select('r');
    }

    /**
     * @param RoleFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {
        return $this->getQuery($filter)
            ->select('count(r)');
    }

    private function getQuery(RoleFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->roleRepository->createQueryBuilder('r');

        if (null !== $filter->getPlayedName()) {
            $qb->andWhere($qb->expr()->like('r.playedName', ':playedName'))
                ->setParameter('playedName', '%' . $filter->getPlayedName() . '%');
        }

        if (null !== $filter->getMovie()) {
            $qb->andWhere($qb->expr()->eq('r.movie', ':movie'))
                ->setParameter('movie', $filter->getMovie());
        }

        if (null !== $filter->getSortByArray()) {
            foreach ($filter->getSortByArray() as $by => $order) {
                $expr = 'desc' == $order
                    ? $qb->expr()->desc('r.' . $by)
                    : $qb->expr()->asc('r.' . $by);
                $qb->addOrderBy($expr);
            }
        }

        return $qb;
    }
}
