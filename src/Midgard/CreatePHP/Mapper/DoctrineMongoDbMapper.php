<?php
namespace Midgard\CreatePHP\Mapper;

use Midgard\CreatePHP\Entity\CollectionInterface;
use Midgard\CreatePHP\Entity\EntityInterface;

use \RuntimeException;

/**
 * Mapper to handle Doctrine Mongodb-ODM.
 */
class DoctrineMongoDbMapper  extends AbstractRdfMapper
{
    /**
     * @var dm Document manager
     */
    protected $dm;

    /**
     * Constrcutor
     * @param $typeMap
     * @param $mongo
     */
    public function __construct($typeMap, $mongo)
    {
        parent::__construct($typeMap);
        $this->dm = $mongo->getManager();
    }

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
        $this->dm->persist($entity->getObject());
        $this->dm->flush();
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

        return $this
            ->dm
            ->getRepository($ids[0])
            ->find($ids[1]);
    }

    /**
     * {@inheritdoc}
     *
     * Model instance primary key and Propel class name are needed to retrieve the model.
     * Primary is json encoded and separated with a «|» from the class name
     */
    public function createSubject($object)
    {
        $parts = explode('\\', get_class($object));
        return $parts[0].$parts[1].':'.end($parts) . "|" .  $object->getId();
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
