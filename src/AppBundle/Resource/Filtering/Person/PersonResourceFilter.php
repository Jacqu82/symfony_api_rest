<?php

namespace AppBundle\Resource\Filtering\Person;

use AppBundle\Repository\PersonRepository;
use AppBundle\Resource\Filtering\ResourceFilterInterface;
use Doctrine\ORM\QueryBuilder;

class PersonResourceFilter implements ResourceFilterInterface
{
    private $repository;

    public function __construct(PersonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param PersonFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('p');

        return $qb;
    }

    /**
     * @param PersonFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('count(p)');

        return $qb;
    }

    private function getQuery(PersonFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('p');

        if (null !== $filter->getFirstName()) {
            $qb->andWhere($qb->expr()->like('p.firstName', ':firstName'))
                ->setParameter('firstName', '%' . $filter->getFirstName() . '%');
        }

        if (null !== $filter->getLastName()) {
            $qb->andWhere($qb->expr()->like('p.lastName', ':lastName'))
                ->setParameter('lastName', '%' . $filter->getFirstName() . '%');
        }

        if (null !== $filter->getBirthFrom()) {
            $qb->andWhere($qb->expr()->gte('p.dateOfBirth', ':birthFrom'))
                ->setParameter('birthFrom', $filter->getBirthFrom());
        };

        if (null !== $filter->getBirthTo()) {
            $qb->andWhere($qb->expr()->lte('p.dateOfBirth', ':birthTo'))
                ->setParameter('birthTo', $filter->getBirthTo());
        }

        if (null !== $filter->getSortByArray()) {
            foreach ($filter->getSortByArray() as $by => $order) {
                $expr = 'desc' == $order
                    ? $qb->expr()->desc('p.' . $by)
                    : $qb->expr()->asc('p.' . $by);
                $qb->addOrderBy($expr);
            }
        }

        return $qb;
    }
}
