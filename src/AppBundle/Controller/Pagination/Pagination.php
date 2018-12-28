<?php

namespace AppBundle\Controller\Pagination;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    private const KEY_LIMIT = 'limit';
    private const KEY_PAGE = 'page';
    private const DEFAULT_LIMIT = 5;
    private const DEFAULT_PAGE = 1;

    private $doctrineRegistry;

    public function __construct(RegistryInterface $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function paginate(
        Request $request,
        string $entityName,
        array $criteria,
        string $countMethod,
        array $countMethodParams,
        string $route,
        array $routeParams
    ): PaginatedRepresentation
    {
        $limit = $request->get(self::KEY_LIMIT, self::DEFAULT_LIMIT);
        $page = $request->get(self::KEY_PAGE, self::DEFAULT_PAGE);
        $offset = ($page - 1) * $limit;

        $repository = $this->doctrineRegistry->getRepository($entityName);
        $resources = $repository->findBy($criteria, [], $limit, $offset);

        if (!method_exists($repository, $countMethod)) {
            throw new \InvalidArgumentException("Entity repository method $countMethod does not exist");
        }

        $resourcesCount = $repository->{$countMethod}(...$countMethodParams);
        $pageCount = (int)ceil($resourcesCount / $limit);
        $collection = new CollectionRepresentation($resources);

        return new PaginatedRepresentation(
            $collection,
            $route,
            $routeParams,
            $page,
            $limit,
            $pageCount
        );
    }
}
