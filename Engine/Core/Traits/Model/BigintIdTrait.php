<?php

namespace Oforge\Engine\Core\Traits\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait BigintIdTrait
 *
 * @package Oforge\Engine\Core\Traits\Model
 */
trait BigintIdTrait {
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="bigint", options={"unsigned": true})
     */
    private $id;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

}
