<?php

namespace Oforge\Engine\Core\Traits\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Trait TimestampsTrait
 *
 * @package Oforge\Engine\Core\Traits\Model
 */
trait TimestampsTrait {
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
