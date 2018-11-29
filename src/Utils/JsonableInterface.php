<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Utils;

/**
 * Interface JsonableInterface
 *
 * @category Micro\BaseComponent
 * @package Setter
 */
interface JsonableInterface
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);
}
