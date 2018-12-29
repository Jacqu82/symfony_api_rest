<?php

namespace AppBundle\Resource\Filtering\Movie;

class MovieFilterDefinition
{
    private $title;
    private $yearFrom;
    private $yearTo;
    private $timeFrom;
    private $timeTo;

    public function __construct(?string $title, ?int $yearFrom, ?int $yearTo, ?int $timeFrom, ?int $timeTo)
    {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
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

    public function getQueryParams(): array
    {
        return get_object_vars($this);
    }
}
