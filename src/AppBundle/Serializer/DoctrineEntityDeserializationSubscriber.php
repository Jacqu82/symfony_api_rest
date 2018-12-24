<?php

namespace AppBundle\Serializer;

use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{

    private $annotationReader;
    private $doctrineRegistry;

    public function __construct(Reader $annotationReader, RegistryInterface $doctrineRegistry)
    {
        $this->annotationReader = $annotationReader;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        //dump($event->getData());
        $deserializedType = $event->getType()['name'];

        if (!class_exists($deserializedType)) {
            return;
        }

        $data = $event->getData();
        $class = new \ReflectionClass($deserializedType);

        //dump($class); die;

        foreach ($class->getProperties() as $property) {
            if (!isset($data[$property->name])) {
                continue;
            }

            /** @var DeserializeEntity $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            $data[$property->name] = [
                $annotation->idField => $data[$property->name]
            ];

            //dump($data); die;
        }
        $event->setData($data);
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $deserializedType = $event->getType()['name'];
        if (!class_exists($deserializedType)) {
            return;
        }

        $object = $event->getObject();
        $reflection = new \ReflectionObject($object);
        //dump($reflection); die;
        foreach ($reflection->getProperties() as $property) {
            /** @var DeserializeEntity $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            if (!$reflection->hasMethod($annotation->setter)) {
                throw new \LogicException(
                    "Object {$reflection->getName()} does not have {$annotation->setter} method"
                );
            }

            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);

            //dump($annotation->idGetter); die;
            $entityId = $deserializedEntity->{$annotation->idGetter}();
            //dump($entityId); die;
            $repository = $this->doctrineRegistry->getRepository($annotation->type);
            //dump($repository);die;

            $entity = $repository->find($entityId);
            if (null === $entity) {
                throw new NotFoundHttpException("Resource {$reflection->getShortName()}/$entityId");
            }

            $object->{$annotation->setter}($entity);
        }
    }
}
