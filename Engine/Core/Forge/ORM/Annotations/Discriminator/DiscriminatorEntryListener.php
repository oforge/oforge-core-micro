<?php

namespace Oforge\Engine\Core\Forge\ORM\Annotations\Discriminator;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\ORMException;
use ReflectionClass;
use ReflectionException;

/**
 * Class DiscriminatorEntryListener
 *
 * @package Oforge\Engine\Cronjob\Test
 */
class DiscriminatorEntryListener implements EventSubscriber {
    const ANNOTATION_ENTRY  = DiscriminatorEntry::class;
    const ANNOTATION_PARENT = DiscriminatorColumn::class;
    /**
     * @var array $discriminatorMaps
     */
    private $discriminatorMaps = [];
    /**
     * @var array $annotations
     */
    private $annotations = [];

    /**
     * Register this class in EntityManager.
     *
     * @param EntityManager $entityManager
     */
    public static function register(EntityManager $entityManager) {
        $entityManager->getEventManager()->addEventSubscriber(new static());
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() : array {
        return [Events::loadClassMetadata];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     *
     * @throws ORMException
     * @throws ReflectionException
     * @throws AnnotationException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs) {
        $class  = $eventArgs->getClassMetadata()->getName();
        $driver = $eventArgs->getEntityManager()->getConfiguration()->getMetadataDriverImpl();
        // Is it DiscriminatorMap parent class?
        // DiscriminatorSubscriber::loadClassMetadata processes only parent classes
        if (!$this->isDiscriminatorParent($class)) {
            return;
        }
        // Register our discriminator class
        $this->discriminatorMaps[$class] = [];
        // And find all subclasses for this parent class
        foreach ($driver->getAllClassNames() as $name) {
            if ($this->isDiscriminatorChild($class, $name)) {
                $this->discriminatorMaps[$class][] = $name;
            }
        }
        // Collect $discriminatorMap for ClassMetadata
        $discriminatorMap = [];
        foreach ($this->discriminatorMaps[$class] as $childClass) {
            /** @var DiscriminatorEntry $annotation */
            $annotation         = $this->getAnnotationForCass($childClass, self::ANNOTATION_ENTRY);
            $discriminatorValue = $this->getDiscriminatorValue($childClass, $annotation);

            $discriminatorMap[$discriminatorValue] = $childClass;
        }
        // $discriminatorValue can be null ot not
        /** @var DiscriminatorEntry $parentAnnotation */
        $parentAnnotation = $this->getAnnotationForCass($class, self::ANNOTATION_ENTRY);
        if (isset($parentAnnotation)) {
            $discriminatorValue = $this->getDiscriminatorValue($class, $parentAnnotation);

            $discriminatorMap[$discriminatorValue] = $class;
        } else {
            $discriminatorValue = null;
        }
        $eventArgs->getClassMetadata()->discriminatorValue = $discriminatorValue;
        $eventArgs->getClassMetadata()->discriminatorMap   = $discriminatorMap;
    }

    /**
     * @param string $class
     * @param string $annotationName
     *
     * @return Annotation|null
     * @throws ReflectionException
     */
    protected function getAnnotationForCass(string $class, string $annotationName) {
        $reflectionClass = new ReflectionClass($class);
        if (isset($this->annotations[$reflectionClass->getName()][$annotationName])) {
            return $this->annotations[$reflectionClass->getName()][$annotationName];
        }
        $reader = new AnnotationReader();
        /** @var Annotation|null $annotation */
        $annotation = $reader->getClassAnnotation($reflectionClass, $annotationName);
        if (isset($annotation)) {
            $this->annotations[$reflectionClass->getName()][$annotationName] = $annotation;
        }

        return $annotation;
    }

    /**
     * @param string $class
     *
     * @return bool
     * @throws ReflectionException
     */
    protected function isDiscriminatorParent(string $class) {
        if (!$this->getAnnotationForCass($class, self::ANNOTATION_PARENT)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $class
     * @param DiscriminatorEntry|null $discriminatorEntry
     *
     * @return mixed
     */
    protected function getDiscriminatorValue(string $class, ?DiscriminatorEntry $discriminatorEntry) {
        return $discriminatorEntry->getValue() ?? str_replace('\\', '_', $class);
    }

    /**
     * @param string $parentClass
     * @param string $currentClass
     *
     * @return bool
     * @throws ReflectionException
     * @throws AnnotationException
     */
    private function isDiscriminatorChild(string $parentClass, string $currentClass) {
        $reflectionClass       = new ReflectionClass($currentClass);
        $parentReflectionClass = $reflectionClass->getParentClass();
        if ($parentReflectionClass === false) {
            return false;
        } elseif ($parentReflectionClass->getName() !== $parentClass) {
            return $this->isDiscriminatorChild($parentReflectionClass->getName(), $currentClass);
        }
        if (is_null($this->getAnnotationForCass($currentClass, self::ANNOTATION_ENTRY))) {
            return false;
        }

        return true;
    }

}
