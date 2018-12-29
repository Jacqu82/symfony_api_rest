<?php

namespace AppBundle\Resource\Filtering\Role;

use AppBundle\Resource\Filtering\AbstractFilterDefinition;
use AppBundle\Resource\Filtering\FilterDefinitionInterface;
use AppBundle\Resource\Filtering\SortableFilterDefinitionInterface;

class RoleFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{
    private $playedName;
    private $movie;
    private $sortBy;
    private $sortByArray;

    public function __construct(?string $playedName, ?int $movie, ?string $sortByQuery, ?array $sortByArray)
    {
        $this->playedName = $playedName;
        $this->movie = $movie;
        $this->sortBy = $sortByQuery;
        $this->sortByArray = $sortByArray;
    }

    public function getPlayedName(): ?string
    {
        return $this->playedName;
    }

    public function getMovie(): ?int
    {
        return $this->movie;
    }

    public function getSortByQuery(): ?string
    {
        return $this->sortBy;
    }

    public function getSortByArray(): ?array
    {
        return $this->sortByArray;
    }
}
