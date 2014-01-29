<?php
/**
 * @copyright CONTENT CONTROL GmbH, http://www.contentcontrol-berlin.de
 * @author Mathieu Rogelja
 * @license Dual licensed under the MIT (MIT-LICENSE.txt) and LGPL (LGPL-LICENSE.txt) licenses.
 * @package Midgard.CreatePHP
 */

namespace Midgard\CreatePHP\Mapper;

use Midgard\CreatePHP\Entity\CollectionInterface;
use Midgard\CreatePHP\Entity\EntityInterface;

/**
 * Base mapper for Propel ORM
 *
 * @package Midgard.CreatePHP
 */
class PropelMapper extends AbstractRdfMapper
{
    /**
     * {@inheritDoc}
     *
     * Persist and flush (persisting an already managed document has no effect
     * and does not hurt).
     *
     * @throws \Exception will throw some exception if storing fails, type is
     *      depending on the doctrine implemenation.
     */
    public function store(EntityInterface $entity)
    {
        $entity->getObject()->save();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySubject($subject)
    {
        if (empty($subject)) {
            throw new RuntimeException('Subject may not be empty');
        }

        $ids = explode('|', $subject);

        if (count($ids) != 2) {
            throw new RuntimeException("Invalid subject: $subject");
        }

        $queryClass = str_replace('/', '\\', $ids[0]) . 'Query';
        $pk    = json_decode(urldecode($ids[1]));

        $model = $queryClass::create()
            ->findPk($pk);

        return $model;
    }

    /**
     * {@inheritdoc}
     *
     * Model instance primary key and Propel class name are needed to retrieve the model.
     * Primary is json encoded and separated with a «|» from the class name
     */
    public function createSubject($object)
    {
        return $this->canonicalName(get_class($object)) . "|" .  urlencode(json_encode($object->getPrimaryKey()));
    }

    /**
     * Reorder the children of the collection node according to the expected order
     *
     * @param EntityInterface $entity
     * @param CollectionInterface $node
     * @param $expectedOrder array of subjects
     * @return void
     */
    public function orderChildren(EntityInterface $entity, CollectionInterface $node, $expectedOrder)
    {
        // Not supported yet.
    }
}