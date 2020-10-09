<?php

namespace Oforge\Engine\File\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Core\Abstracts\AbstractModel;

/**
 * Class FileUsage
 *
 * @package Oforge\Engine\File\Model
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="oforge_file_usages")
 */
class FileUsage extends AbstractModel {
    //TODO change: fileID, class, entityID, property(path)
    /**
     * @var int $fileID
     * @ORM\Column(name="file_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fileID;
    /**
     * @var int $usageAmount Usage amount.
     * @ORM\Column(name="usage_amount", type="integer", nullable=false)
     */
    private $usageAmount = 0;

    /**
     * @return int
     */
    public function getFileID() : int {
        return $this->fileID;
    }

    /**
     * @param int $fileID
     *
     * @return FileUsage
     */
    protected function setFileID(int $fileID) : FileUsage {
        if (!isset($this->fileID)) {
            $this->fileID = $fileID;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getUsageAmount() : int {
        return $this->usageAmount;
    }

    /**
     * @return FileUsage
     */
    public function incUsageAmount() : FileUsage {
        $this->usageAmount++;

        return $this;
    }

    /**
     * @return FileUsage
     */
    public function decUsageAmount() : FileUsage {
        if ($this->usageAmount > 0) {
            $this->usageAmount--;
        }

        return $this;
    }

}
