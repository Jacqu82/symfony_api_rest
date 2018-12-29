<?php

namespace AppBundle\Resource\Filtering\Movie;

use AppBundle\Repository\MovieRepository;
use AppBundle\Resource\Filtering\ResourceFilterInterface;
use Doctrine\ORM\QueryBuilder;

class MovieResourceFilter implements ResourceFilterInterface
{
    private $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        return $this->getQuery($filter)
            ->select('m');
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {
        return $this->getQuery($filter)
            ->select('count(m)');
    }

    private function getQuery(MovieFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->movieRepository->createQueryBuilder('m');

        if (null !== $filter->getTitle()) {
            $qb->andWhere($qb->expr()->like('m.title', ':title'))
                ->setParameter('title', '%' . $filter->getTitle() . '%');
        }

        if (null !== $filter->getYearFrom()) {
            $qb->andWhere($qb->expr()->gte('m.year', ':yearFrom'))
                ->setParameter('yearFrom', $filter->getYearFrom());
        }

        if (null !== $filter->getYearTo()) {
            $qb->andWhere($qb->expr()->lte('m.year', ':yearTo'))
                ->setParameter('yearTo', $filter->getYearTo());
        }

        if (null !== $filter->getTimeFrom()) {
            $qb->andWhere($qb->expr()->gte('m.time', ':timeFrom'))
                ->setParameter('timeFrom', $filter->getTimeFrom());
        }

        if (null !== $filter->getTimeTo()) {
            $qb->andWhere($qb->expr()->lte('m.time', ':timeTo'))
                ->setParameter('timeTo', $filter->getTimeTo());
        }

        if (null !== $filter->getSortByArray()) {
            foreach ($filter->getSortByArray() as $by => $order) {
                $expr = 'desc' == $order
                    ? $qb->expr()->desc('m.' . $by)
                    : $qb->expr()->asc('m.' . $by);
                $qb->addOrderBy($expr);
            }
        }

        return $qb;
    }
}
