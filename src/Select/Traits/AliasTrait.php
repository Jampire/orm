<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\ORM\Select\Traits;

trait AliasTrait
{
    /** @var array */
    private $aliasPaths = [];

    /**
     * @param string $relation
     * @return string
     */
    private function resolvePath(string $relation): string
    {
        return $this->aliasPaths[$relation] ?? $relation;
    }

    /**
     * @param string $alias
     * @param string $relation
     */
    private function registerPath(string $alias, string $relation)
    {
        $this->aliasPaths[$alias] = $relation;
    }
}