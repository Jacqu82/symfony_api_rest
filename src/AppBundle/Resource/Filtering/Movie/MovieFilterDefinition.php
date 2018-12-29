<?php

namespace AppBundle\Resource\Filtering\Movie;

use AppBundle\Resource\Filtering\AbstractFilterDefinition;
use AppBundle\Resource\Filtering\FilterDefinitionInterface;
use AppBundle\Resource\Filtering\SortableFilterDefinitionInterface;

class MovieFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{
    private $title;
    private $yearFrom;
    private $yearTo;
    private $timeFrom;
    private $timeTo;
    private $sortBy;
    private $sortByArray;

    public function __construct(?string $title, ?int $yearFrom, ?int $yearTo, ?int $timeFrom, ?int $timeTo, ?string $sortBy, ?array $sortByArray)
    {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
        $this->sortBy = $sortBy;
        $this->sortByArray = $sortByArray;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getYearFrom(): ?int
    {
        return $this->yearFrom;
    }

    public function getYearTo(): ?int
    {
        return $this->yearTo;
    }

    public function getTimeFrom(): ?int
    {
        return $this->timeFrom;
    }

    public function getTimeTo(): ?int
    {
        return $this->timeTo;
    }

    public function getSortByQuery(): ?string
    {
        return $this->sortBy;
    }

    public function getSortByArray(): ?array
    {
        return $this->sortByArray;
    }

    public function getParameters(): array
    {
        return get_object_vars($this);
    }
}
