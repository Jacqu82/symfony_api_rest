<?php

namespace AppBundle\Resource\Filtering;


interface FilterDefinitionInterface
{
    public function getQueryParams(): array;
    public function getQueryParamsBlacklist(): array;
    public function getParameters(): array;
}