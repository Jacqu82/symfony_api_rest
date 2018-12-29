<?php

namespace AppBundle\Resource\Pagination\Movie;

use AppBundle\Resource\Filtering\Movie\MovieFilterDefinition;
use AppBundle\Resource\Filtering\Movie\MovieResourceFilter;
use AppBundle\Resource\Pagination\Page;
use Doctrine\ORM\UnexpectedResultException;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class MoviePagination
{
    private $resourceFilter;

    public function __construct(MovieResourceFilter $resourceFilter)
    {
        $this->resourceFilter = $resourceFilter;
    }

    public function paginate(Page $page, MovieFilterDefinition $filter): PaginatedRepresentation
    {
        $resources = $this->resourceFilter->getResources($filter)
            ->setFirstResult($page->getOffset())
            ->setMaxResults($page->getLimit())
            ->getQuery()
            ->getResult();

        $resourceCount = $pages = null;

        try {
            $resourceCount = $this->resourceFilter->getResourceCount($filter)
                ->getQuery()
                ->getSingleScalarResult();
            $pages = ceil($resourceCount / $page->getLimit());
        } catch (UnexpectedResultException $exception) {

        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($resources),
            'get_movies',
            $filter->getQueryParams(),
            $page->getPage(),
            $page->getLimit(),
            $pages,
            null,
            null,
            false,
            $resourceCount
        );
    }
}
