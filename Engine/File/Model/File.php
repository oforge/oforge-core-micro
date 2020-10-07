<?php

namespace Oforge\Engine\File\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class File
 *
 * @package Oforge\Engine\FileUpload\Model
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="oforge_files")
 */
class File extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $typeGroup
     * @ORM\Column(name="type_group", type="string", nullable=false)
     */
    private $typeGroup;
    /**
     * @var string $mimeType
     * @ORM\Column(name="mime_type", type="string", nullable=false)
     */
    private $mimeType;
    /**
     * @var string $filePath
     * @ORM\Column(name="file_path", type="text", nullable=false)
     */
    private $filePath;
    /**
     * @var string|null $uploaderID
     * @ORM\Column(name="uploader_id", type="string", nullable=true)
     */
    private $uploaderID = null;
    /**
     * @var DateTimeImmutable $createdAt
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;
    /**
     * @var DateTimeImmutable $updatedAt
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;

    /**
     * File constructor.
     */
    public function __construct() {
        $this->updatedTimestamps();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() : void {
        $dateTimeNow     = new DateTimeImmutable('now');
        $this->updatedAt = $dateTimeNow;
        if ($this->createdAt === null) {
            $this->createdAt = $dateTimeNow;
        }
    }

    /**
     * @return int
     */
    public function getID() : int {
        return $this->id;
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
     * @return File
     */
    public function setTypeGroup(string $typeGroup) : File {
        $this->typeGroup = $typeGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType() : string {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType(string $mimeType) : File {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath() : string {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     *
     * @return File
     */
    public function setFilePath(string $filePath) : File {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUploaderID() : ?string {
        return $this->uploaderID;
    }

    /**
     * @param string|null $uploaderID
     *
     * @return File
     */
    public function setUploaderID(?string $uploaderID) : File {
        $this->uploaderID = $uploaderID;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt() : DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt() : DateTimeImmutable {
        return $this->updatedAt;
    }

}