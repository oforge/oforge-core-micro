<?php

namespace Oforge\Engine\File\Model;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class FileMimeType
 *
 * @package Oforge\Engine\FileUpload\Model
 * @ORM\Entity
 * @ORM\Table(name="oforge_file_mime_types")
 */
class FileMimeType extends AbstractModel {
    /**
     * @var string $id
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;
    /**
     * @var string|null $fileExtension
     * @ORM\Column(name="file_extension", type="string", nullable=true)
     */
    private $fileExtension = null;
    /**
     * @var string $typeGroup
     * @ORM\Column(name="type_group", type="string", nullable=false)
     */
    private $typeGroup;
    /**
     * @var bool $allowed
     * @ORM\Column(name="allowed", type="boolean", options={"default":false})
     */
    private $allowed = false;

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFileExtension() : ?string {
        return $this->fileExtension;
    }

    /**
     * @param string|null $fileExtension
     *
     * @return FileMimeType
     */
    public function setFileExtension(?string $fileExtension) : FileMimeType {
        if (!isset($this->fileExtension)) {
            $this->fileExtension = $fileExtension;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowed() : bool {
        return $this->allowed;
    }

    /**
     * @param bool $allowed
     *
     * @return FileMimeType
     */
    public function setAllowed(bool $allowed) : FileMimeType {
        $this->allowed = $allowed;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeGroup() : string {
        return $this->typeGroup;
    }

    /**
     * @param string $typeGroup
     *
     * @return FileMimeType
     */
    public function setTypeGroup(string $typeGroup) : FileMimeType {
        if (!isset($this->typeGroup)) {
            $this->typeGroup = $typeGroup;
        }

        return $this;
    }

    /**
     * @param string $id
     *
     * @return FileMimeType
     */
    protected function setMimeType(string $mimeType) : FileMimeType {
        if (!isset($this->id)) {
            $this->id = $mimeType;
        }

        return $this;
    }

}
