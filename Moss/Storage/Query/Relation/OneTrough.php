<?php
namespace Moss\Storage\Query\Relation;

use Moss\Storage\Query\QueryInterface;

/**
 * One to one relation representation
 *
 * @package Moss Storage
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class OneTrough extends Relation
{
    /**
     * Executes read for one-to-one relation
     *
     * @param array|\ArrayAccess $result
     *
     * @return array|\ArrayAccess
     */
    public function read(&$result)
    {
        $relations = array();
        $conditions = array();

        foreach ($this->relation->foreignValues() as $refer => $value) {
            $conditions[$refer][] = $value;
        }

        foreach ($result as $i => $entity) {
            if (!$this->assertEntity($entity)) {
                continue;
            }

            if (!$this->accessProperty($entity, $this->relation->container())) {
                $this->accessProperty($entity, $this->relation->container(), null);
            }

            foreach ($this->relation->localKeys() as $local => $refer) {
                $conditions[$refer][] = $this->accessProperty($entity, $local);
            }

            $relations[$this->buildLocalKey($entity, $this->relation->localKeys())][] = & $result[$i];
        }

        $collection = $this->fetch($this->relation->mediator(), $conditions);

// --- MEDIATOR START

        $mediator = array();
        $conditions = array();
        foreach ($collection as $entity) {
            foreach ($this->relation->foreignKeys() as $local => $refer) {
                $conditions[$refer][] = $this->accessProperty($entity, $local);
            }

            $in = $this->buildForeignKey($entity, $this->relation->localKeys());
            $out = $this->buildLocalKey($entity, $this->relation->foreignKeys());
            $mediator[$out] = $in;
        }

        $collection = $this->fetch($this->relation->entity(), $conditions);

// --- MEDIATOR END

        foreach ($collection as $relEntity) {
            $key = $this->buildForeignKey($relEntity, $this->relation->foreignKeys());

            if (!isset($mediator[$key]) || !isset($relations[$mediator[$key]])) {
                continue;
            }

            foreach ($relations[$mediator[$key]] as &$entity) {
                $entity->{$this->relation->container()} = $relEntity;
                unset($entity);
            }
        }

        return $result;
    }

    /**
     * Executes write fro one-to-one relation
     *
     * @param array|\ArrayAccess $result
     *
     * @return array|\ArrayAccess
     */
    public function write(&$result)
    {
        if (!isset($result->{$this->relation->container()})) {
            return $result;
        }

        $entity = & $result->{$this->relation->container()};

        $query = clone $this->query;
        $query->operation(QueryInterface::OPERATION_WRITE, $this->relation->entity(), $entity)
            ->execute();

        $fields = array_merge(array_values($this->relation->localKeys()), array_keys($this->relation->foreignKeys()));
        $mediator = array();

        foreach ($this->relation->localKeys() as $local => $foreign) {
            $mediator[$foreign] = $this->accessProperty($result, $local);
        }

        foreach ($this->relation->foreignKeys() as $foreign => $local) {
            $mediator[$foreign] = $this->accessProperty($entity, $local);
        }

        $query = clone $this->query;
        $query->operation(QueryInterface::OPERATION_WRITE, $this->relation->mediator(), $mediator)
            ->fields($fields)
            ->execute();

        $conditions = array();
        foreach ($this->relation->localKeys() as $foreign) {
            $conditions[$foreign][] = $this->accessProperty($mediator, $foreign);
        }

        $this->cleanup($this->relation->mediator(), array($mediator), $conditions);

        return $result;
    }

    /**
     * Executes delete for one-to-one relation
     *
     * @param array|\ArrayAccess $result
     *
     * @return array|\ArrayAccess
     */
    public function delete(&$result)
    {
        if (!isset($result->{$this->relation->container()})) {
            return $result;
        }

        $mediator = array();

        foreach ($this->relation->localKeys() as $entityField => $mediatorField) {
            $mediator[$mediatorField] = $this->accessProperty($result, $entityField);
        }

        $entity = $result->{$this->relation->container()};
        foreach ($this->relation->foreignKeys() as $mediatorField => $entityField) {
            $mediator[$mediatorField] = $this->accessProperty($entity, $entityField);
        }

        $query = clone $this->query;
        $query->operation(QueryInterface::OPERATION_DELETE, $this->relation->mediator(), $mediator)
            ->execute();

        return $result;
    }

    /**
     * Executes clear for one-to-many relation
     */
    public function clear()
    {
        $query = clone $this->query;
        $query->operation(QueryInterface::OPERATION_CLEAR, $this->relation->mediator())
            ->execute();
    }
}
