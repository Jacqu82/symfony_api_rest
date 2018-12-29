<?php

namespace AppBundle\Resource\Filtering\Person;

use AppBundle\Resource\Filtering\AbstractFilterDefinition;
use AppBundle\Resource\Filtering\FilterDefinitionInterface;
use AppBundle\Resource\Filtering\SortableFilterDefinitionInterface;

class PersonFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface, SortableFilterDefinitionInterface
{
    private $firstName;
    private $lastName;
    private $birthFrom;
    private $birthTo;
    private $sortBy;
    private $sortByArray;

    public function __construct(?string $firstName, ?string $lastName, ?string $birthFrom, ?string $birthTo, ?string $sortByQuery, ?array $sortByArray)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthFrom = $birthFrom;
        $this->birthTo = $birthTo;
        $this->sortBy = $sortByQuery;
        $this->sortByArray = $sortByArray;
    }

    public function getParameters(): array
    {
        return get_object_vars($this);
    }

    public function getSortByQuery(): ?string
    {
        return $this->sortBy;
    }

    public function getSortByArray(): ?array
    {
        return $this->sortByArray;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getBirthFrom(): ?string
    {
        return $this->birthFrom;
    }

    public function getBirthTo(): ?string
    {
        return $this->birthTo;
    }
}
