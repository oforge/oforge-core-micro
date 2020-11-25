<?php

namespace Oforge\Engine\Core\Traits\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait StringIdTrait
 *
 * @package Oforge\Engine\Core\Traits\Model
 */
trait StringIdTrait {
    /**
     * @var string $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     */
    private $id;

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public function setId(string $id) {
        if (!isset($this->id)) {
            $this->id = $id;
        }

        return $this;
    }

}
