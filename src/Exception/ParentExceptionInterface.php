<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Exception;

/**
 * Interface ParentExceptionInterface
 *
 * @package Micro\BaseComponent
 * @category Exception
 */
interface ParentExceptionInterface
{
    const CONTEXT_SELIALIZE_NONE    = 'none';
    const CONTEXT_SELIALIZE_BASIC   = 'serialize';
    const CONTEXT_SELIALIZE_JSON    = 'json';
    const CONTEXT_SELIALIZE_PRINTR  = 'printr';

    /**
     * Return parent exception
     *
     * @return \Exception
     */
    public function getParentException(): \Exception;

    /**
     * Return context exception array from parent exception object
     *
     * @param array $context
     * @param string $contextSerializeType
     *
     * @return array
     */
    public function getParentExceptionContext(array $context = [], string $contextSerializeType = ParentExceptionInterface::CONTEXT_SELIALIZE_NONE): array;
}