<?php

namespace Oforge\Engine\File\Traits\Model;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\File\Services\FileUsageService;

/**
 * Trait FilePropertyUsageHandlingTrait
 *
 * @package Oforge\Engine\File\Models
 * @ORM\HasLifecycleCallbacks must be in added in target model!!!
 */
trait FilePropertyUsageChangeListenerTrait {
    /** @var array $traitFilePropertyUsageUpdates */
    private $traitFilePropertyUsageUpdates = [];

    /**
     * @param int|null $oldFileID
     * @param int|null $newFileID
     * @param string $entityProperty
     * @param string|null $arrayPropertyPath
     * @param string $entityIdPropertyName
     */
    function onFilePropertyChanged(
        ?int $oldFileID,
        ?int $newFileID,
        string $entityProperty,
        ?string $arrayPropertyPath = null,
        string $entityIdPropertyName = 'id'
    ) {
        if ($oldFileID === $newFileID) {
            return;
        }
        $this->traitFilePropertyUsageUpdates[] = [
            'oldFileID'         => $oldFileID,
            'newFileID'         => $newFileID,
            'entityID'          => $entityIdPropertyName,
            'entityProperty'    => $entityProperty,
            'arrayPropertyPath' => $arrayPropertyPath,
        ];
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function processTraitFilePropertyUsageUpdates() {
        if (!empty($this->traitFilePropertyUsageUpdates)) {
            /** @var FileUsageService $fileUsageService */
            $fileUsageService = Oforge()->Services()->get('file.usage');
            foreach ($this->traitFilePropertyUsageUpdates as $index => &$entry) {
                $idProperty = $entry['entityID'];
                $fileUsageService->updateUsage(#
                    $entry['oldFileID'],#
                    $entry['newFileID'],#
                    $this->$idProperty,#
                    static::class,#
                    $entry['entityProperty'],#
                    $entry['arrayPropertyPath']#
                );
            }
            $this->traitFilePropertyUsageUpdates = [];
        }
    }

}
