<?php

namespace Oforge\Engine\File\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class FileUsage
 *
 * @package Oforge\Engine\File\Model
 * @ORM\Entity
 * @ORM\Table(name="oforge_file_usages")
 */
class FileUsage extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var int $fileID
     * @ORM\Column(name="file_id", type="bigint", nullable=false, options={"unsigned"=true})
     */
    private $fileID;
    /**
     * @var string $entityID
     * @ORM\Column(name="entity_id", type="string", nullable=false)
     */
    private $entityID;
    /**
     * @var string $entityClass
     * @ORM\Column(name="entity_class", type="string", length=510, nullable=false)
     */
    private $entityClass;
    /**
     * @var string $entityProperty
     * @ORM\Column(name="entity_property", type="string", nullable=false)
     */
    private $entityProperty;
    /**
     * @var string|null $arrayPropertyPath
     * @ORM\Column(name="array_property_path", type="string", length=510,  nullable=true)
     */
    private $arrayPropertyPath = null;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFileID() : string {
        return $this->fileID;
    }

    /**
     * @param string $fileID
     *
     * @return FileUsage
     */
    public function setFileID(string $fileID) : FileUsage {
        $this->fileID = $fileID;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntityID() : string {
        return $this->entityID;
    }

    /**
     * @param string $entityID
     */
    protected function setEntityID(string $entityID) : void {
        if (!isset($this->entityID)) {
            $this->entityID = $entityID;
        }
    }

    /**
     * @return string
     */
    public function getEntityClass() : string {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    protected function setEntityClass(string $entityClass) : void {
        if (!isset($this->entityClass)) {
            $this->entityClass = $entityClass;
        }
    }

    /**
     * @return string
     */
    public function getEntityProperty() : string {
        return $this->entityProperty;
    }

    /**
     * @param string $entityProperty
     */
    protected function setEntityProperty(string $entityProperty) : void {
        if (!isset($this->entityProperty)) {
            $this->entityProperty = $entityProperty;
        }
    }

    /**
     * @return string|null
     */
    public function getArrayPropertyPath() : ?string {
        return $this->arrayPropertyPath;
    }

    /**
     * @param string|null $arrayPropertyPath
     */
    protected function setArrayPropertyPath(?string $arrayPropertyPath) : void {
        if (!isset($this->arrayPropertyPath)) {
            $this->arrayPropertyPath = $arrayPropertyPath;
        }
    }

}
