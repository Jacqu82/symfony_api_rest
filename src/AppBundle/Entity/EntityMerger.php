<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\Id;

class EntityMerger
{
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function merge($entity, $changes): void
    {
        $entityClassName = get_class($entity);
        if (!$entityClassName) {
            throw new \InvalidArgumentException('Is not a class!');
        }

        $changesClassName = get_class($changes);
        if (!$changesClassName) {
            throw new \InvalidArgumentException('Is not a class!');
        }

        if (!is_a($changes, $entityClassName)) {
            throw new \InvalidArgumentException(
                "Cannot merge object of class $changesClassName with object of class $entityClassName"
            );
        }

        $entityReflection = new \ReflectionObject($entity);
        $changesReflection = new \ReflectionObject($changes);

        foreach ($changesReflection->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            $changedPropertyValue = $changedProperty->getValue($changes);

            if (null === $changedPropertyValue) {
                continue;
            }

            if (!$entityReflection->hasProperty($changedProperty->getName())) {
                continue;
            }

            $entityProperty = $entityReflection->getProperty($changedProperty->getName());
            $annotation = $this->annotationReader->getPropertyAnnotation($entityProperty, Id::class);

            if (null !== $annotation) {
                continue;
            }

            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }
}
