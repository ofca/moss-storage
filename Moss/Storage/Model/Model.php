<?php
namespace Moss\Storage\Model;

use Moss\Storage\Model\Definition\FieldInterface;
use Moss\Storage\Model\Definition\IndexInterface;
use Moss\Storage\Model\Definition\RelationInterface;
use Moss\Storage\Model\Definition\ForeignInterface;

/**
 * Entity model representation for storage
 *
 * @package Moss storage
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Model implements ModelInterface
{

    protected $table;
    protected $entity;

    /** @var array|FieldInterface[] */
    protected $fields = array();

    /** @var array|IndexInterface[] */
    protected $indexes = array();

    /** @var array|RelationInterface[] */
    protected $relations = array();

    /**
     * Constructor
     *
     * @param string                    $entityClass
     * @param string                    $table
     * @param array|FieldInterface[]    $fields
     * @param array|IndexInterface[]    $indexes
     * @param array|RelationInterface[] $relations
     *
     * @throws ModelException
     */
    public function __construct($entityClass, $table, array $fields, array $indexes = array(), array $relations = array())
    {
        $this->table = $table;
        $this->entity = $entityClass ? ltrim($entityClass, '\\') : null;

        $this->assignFields($fields);
        $this->assignIndexes($indexes);
        $this->assignRelations($relations);
    }

    protected function assignFields($fields)
    {
        foreach ($fields as $field) {
            if (!$field instanceof FieldInterface) {
                throw new ModelException(sprintf('Field must be an instance of FieldInterface, got "%s"', $this->getType($field)));
            }

            $field->table($this->table);
            $this->fields[$field->name()] = $field;
        }
    }

    protected function assignIndexes($indexes)
    {
        foreach ($indexes as $index) {
            if (!$index instanceof IndexInterface) {
                throw new ModelException(sprintf('Index must be an instance of IndexInterface, got "%s"', $this->getType($index)));
            }

            foreach ($index->fields() as $key => $field) {
                $field = $index->type() == self::INDEX_FOREIGN ? $key : $field;

                if (!$this->hasField($field)) {
                    throw new ModelException(sprintf('Index field "%s" does not exist in entity model "%s"', $field, $this->entity));
                }
            }

            if ($index->type() !== ModelInterface::INDEX_FOREIGN) {
                $index->table($this->table);
            }

            $this->indexes[$index->name()] = $index;
        }
    }

    protected function assignRelations($relations)
    {
        foreach ($relations as $relation) {
            if (!$relation instanceof RelationInterface) {
                throw new ModelException(sprintf('Relation must be an instance of RelationInterface, got "%s"', $this->getType($relation)));
            }

            foreach ($relation->keys() as $field => $trash) {
                if (!$this->hasField($field)) {
                    throw new ModelException(sprintf('Relation field "%s" does not exist in entity model "%s"', $field, $this->entity));
                }
            }

            foreach ($relation->localValues() as $field => $trash) {
                if (!$this->hasField($field)) {
                    throw new ModelException(sprintf('Relation field "%s" does not exist in entity model "%s"', $field, $this->entity));
                }
            }

            $this->relations[$relation->name()] = $relation;
        }
    }

    private function getType($var)
    {
        if (is_object($var)) {
            return get_class($var);
        }

        return gettype($var);
    }

    /**
     * Returns table
     *
     * @return string
     */
    public function table()
    {
        return $this->table;
    }

    /**
     * Returns entity class name
     *
     * @return string
     */
    public function entity()
    {
        return $this->entity;
    }

    /**
     * Returns true if model has field
     *
     * @param string $field
     *
     * @return bool
     */
    public function hasField($field)
    {
        return isset($this->fields[$field]);
    }

    /**
     * Returns array containing field definition
     *
     * @return array|FieldInterface[]
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Returns field definition
     *
     * @param string $field
     *
     * @return FieldInterface
     * @throws ModelException
     */
    public function field($field)
    {
        if (!$this->hasField($field)) {
            throw new ModelException(sprintf('Field "%s" not found in model "%s"', $field, $this->entity));
        }

        return $this->fields[$field];
    }

    /**
     * Returns true if field is primary index
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function isPrimary($field)
    {
        if (!$this->hasField($field)) {
            throw new ModelException(sprintf('Unknown field "%s" in model "%s"', $field, $this->entity));
        }

        foreach ($this->indexes as $index) {
            if ($index->isPrimary() && $index->hasField($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns array containing names of primary indexes
     *
     * @return array|FieldInterface[]
     */
    public function primaryFields()
    {
        $result = array();
        foreach ($this->indexes as $index) {
            if (!$index->isPrimary()) {
                continue;
            }

            foreach ($index->fields() as $field) {
                $result[] = $this->field($field);
            }
        }

        return $result;
    }

    /**
     * Returns true if field is index of any type
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function isIndex($field)
    {
        if (!$this->hasField($field)) {
            throw new ModelException(sprintf('Unknown field "%s" in model "%s"', $field, $this->entity));
        }

        foreach ($this->indexes as $index) {
            if ($index->hasField($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns array containing all indexes in which field appears
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function inIndex($field)
    {
        if (!$this->hasField($field)) {
            throw new ModelException(sprintf('Unknown field "%s" in model "%s"', $field, $this->entity));
        }

        $result = array();
        foreach ($this->indexes as $index) {
            if ($index->hasField($field)) {
                $result[] = $index;
            }
        }

        return $result;
    }

    /**
     * Returns array containing names of indexes
     *
     * @return array|FieldInterface[]
     */
    public function indexFields()
    {
        $result = array();
        foreach ($this->indexes as $index) {
            foreach ($index->fields() as $field) {
                $result[] = $this->field($field);
            }
        }

        return $result;
    }

    /**
     * Returns all index definitions
     *
     * @return IndexInterface[]
     */
    public function indexes()
    {
        return $this->indexes;
    }


    /**
     * Returns index definition
     *
     * @param string $index
     *
     * @return IndexInterface[]
     * @throws ModelException
     */
    public function index($index)
    {
        if (empty($this->indexes[$index])) {
            throw new ModelException(sprintf('Unknown index "%s" in model "%s"', $index, $this->entity));
        }

        return $this->indexes[$index];
    }

    /**
     * Returns true if at last one relation is defined
     *
     * @return bool
     */
    public function hasRelations()
    {
        return !empty($this->relations);
    }

    /**
     * Returns true if relation to passed entity class is defined
     *
     * @param string $relationName
     *
     * @return bool
     */
    public function hasRelation($relationName)
    {
        return isset($this->relations[$relationName]);
    }

    /**
     * Returns all relation definition
     *
     * @return array|RelationInterface[]
     */
    public function relations()
    {
        return $this->relations;
    }

    /**
     * Returns relation definition for passed entity class
     *
     * @param string $relationName
     *
     * @return RelationInterface
     * @throws ModelException
     */
    public function relation($relationName)
    {
        if (!$this->hasRelation($relationName)) {
            throw new ModelException(sprintf('Relation "%s" not found in model "%s"', $relationName, $this->entity));
        }

        return $this->relations[$relationName];
    }
}
