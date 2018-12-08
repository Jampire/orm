<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\ORM\Relation\Morphed;

use Spiral\ORM\Command\CarrierInterface;
use Spiral\ORM\Command\CommandInterface;
use Spiral\ORM\ORMInterface;
use Spiral\ORM\Point;
use Spiral\ORM\Relation;
use Spiral\ORM\Relation\HasOneRelation;
use Spiral\ORM\Util\Promise;

/**
 * Inverted version of belongs to morphed.
 */
class MorphedHasOneRelation extends HasOneRelation
{
    /** @var mixed|null */
    private $morphKey;

    /**
     * @param ORMInterface $orm
     * @param string       $class
     * @param string       $relation
     * @param array        $schema
     */
    public function __construct(ORMInterface $orm, string $class, string $relation, array $schema)
    {
        parent::__construct($orm, $class, $relation, $schema);
        $this->morphKey = $this->define(Relation::MORPH_KEY);
    }

    /**
     * @inheritdoc
     */
    public function initPromise(Point $point): array
    {
        if (empty($innerKey = $this->fetchKey($point, $this->innerKey))) {
            return [null, null];
        }

        $p = new Promise\PromiseOne($this->orm, $this->class, [
            $this->outerKey => $innerKey,
            $this->morphKey => $point->getRole()
        ]);

        return [$p, $p];
    }

    /**
     * @inheritdoc
     */
    public function queueRelation(
        CarrierInterface $parentCommand,
        $parentEntity,
        Point $parentState,
        $related,
        $original
    ): CommandInterface {
        $store = parent::queueRelation($parentCommand, $parentEntity, $parentState, $related, $original);

        if ($store instanceof CarrierInterface && !is_null($related)) {
            $relState = $this->getPoint($related);
            if ($this->fetchKey($relState, $this->morphKey) != $parentState->getRole()) {
                $store->push($this->morphKey, $parentState->getRole(), true);
                $relState->setData([$this->morphKey => $parentState->getRole()]);
            }
        }

        return $store;
    }
}