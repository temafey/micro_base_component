<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Entity;

/**
 * Abstract class AbstractEntity
 *
 * @category Micro\BaseComponent
 * @sub-package Entity
 */
abstract class AbstractEntity
{
    /**
     * @param array $attributes
     *
     * @return $this
     *
     * @throws Exception
     */
    public function fromNative(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->__set($name, $value);
        }

        return $this;
    }

    /**
     * Convert and return all object data to array
     *
     * @param bool $underscore
     *
     * @return array
     *
     * @throws Exception
     * @throws \ReflectionException
     */
    public function toArray(bool $underscore = false): array
    {
        $data = [];
        $properties = (new \ReflectionClass($this))->getProperties();

        foreach ($properties as $property) {
            $name = $property->getName();
            $key = $name;

            if ($underscore) {
                $key = $this->underscore($name);
            }
            $data[$key] = $this->__get($name);
        }

        return $data;
    }

    /**
     * Isset property in entity
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        if (
            !property_exists($this, $name) &&
            !property_exists($this, lcfirst($this->camelize($name)))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Set property if it exists and if it has setter method
     *
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     *
     * @throws Exception
     */
    public function __set(string $name, $value): self
    {
        if (!$this->__isset($name)) {
            throw new Exception(sprintf('Attribute \'%s\' does not exists in the \'%s\' model', $name,
                \get_class($this)));
        }

        $setterMethod = 'set' . $this->camelize($name);

        if (!method_exists($this, $setterMethod)) {
            throw new Exception(sprintf('Setter \'%s\' does not exists in the \'%s\' model', $setterMethod, \get_class($this)));
        }

        return $this->$setterMethod($value);
    }

    /**
     * Return property if it exists and if it has getter method
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new Exception(sprintf('Attribute \'%s\' does not exists in the \'%s\' model', $name, \get_class($this)));
        }

        $getterMethod = 'get' . $this->camelize($name);

        if (!method_exists($this, $getterMethod)) {
            throw new Exception(sprintf('Getter \'%s\' does not exists in the \'%s\' model', $getterMethod, \get_class($this)));
        }

        return $this->$getterMethod();
    }

    /**
     * Return camecase string style
     *
     * @param string $input
     * @param string $separator
     *
     * @return mixed
     */
    public function camelize(string $input, string $separator = '_'): string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    /**
     * Convert string from camelcase to underscore
     *
     * @param string $input
     *
     * @return string
     */
    public function underscore(string $input): string
    {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $input));
    }
}