<?php
declare(strict_types=1);

namespace Micro\BaseComponent\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * Class ColumnHydrator
 *
 * @category Hydrator
 * @package Micro\BaseComponent\Hydrator
 */
class ColumnHydrator extends AbstractHydrator
{
    /**
     * Hydrates all rows from the current statement instance at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}