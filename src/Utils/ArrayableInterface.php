<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

/**
 * Interface ArrayableInterface
 *
 * @category Micro\BaseComponent
 * @package Setter
 */
interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
