<?php
namespace Moss\Storage\Model;

use Moss\Storage\Model\Definition\FieldInterface;
use Moss\Storage\Model\Definition\IndexInterface;
use Moss\Storage\Model\Definition\RelationInterface;

interface ModelInterface
{
    // Index types
    const INDEX_PRIMARY = 'primary';
    const INDEX_FOREIGN = 'foreign';
    const INDEX_INDEX = 'index';
    const INDEX_UNIQUE = 'unique';

    // Relation types
    const RELATION_ONE = 'one';
    const RELATION_MANY = 'many';
    const RELATION_ONE_TROUGH = 'oneTrough';
    const RELATION_MANY_TROUGH = 'manyTrough';

    // Field types
    const FIELD_BOOLEAN = 'boolean';
    const FIELD_INTEGER = 'integer';
    const FIELD_DECIMAL = 'decimal';
    const FIELD_STRING = 'string';
    const FIELD_DATETIME = 'datetime';
    const FIELD_SERIAL = 'serial';

    // Attributes
    const ATTRIBUTE_UNSIGNED = 'unsigned';
    const ATTRIBUTE_DEFAULT = 'default';
    const ATTRIBUTE_AUTO = 'auto_increment';
    const ATTRIBUTE_NULL = 'null';
    const ATTRIBUTE_LENGTH = 'length';
    const ATTRIBUTE_PRECISION = 'precision';

    /**
     * Returns table
     *
     * @return string
     */
    public function table();

    /**
     * Returns entity class name
     *
     * @return string
     */
    public function entity();

    /**
     * Returns true if model has field
     *
     * @param string $field
     *
     * @return bool
     */
    public function hasField($field);

    /**
     * Returns array containing field definition
     *
     * @return array|FieldInterface[]
     */
    public function fields();

    /**
     * Returns field definition
     *
     * @param string $field
     *
     * @return FieldInterface
     * @throws ModelException
     */
    public function field($field);

    /**
     * Returns true if field is primary index
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function isPrimary($field);

    /**
     * Returns array containing names of primary indexes
     *
     * @return array|FieldInterface[]
     */
    public function primaryFields();

    /**
     * Returns true if field is index of any type
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function isIndex($field);

    /**
     * Returns array containing all indexes in which field appears
     *
     * @param string $field
     *
     * @return bool
     * @throws ModelException
     */
    public function inIndex($field);

    /**
     * Returns array containing names of indexes
     *
     * @return array|FieldInterface[]
     */
    public function indexFields();

    /**
     * Returns all index definitions
     *
     * @return IndexInterface[]
     */
    public function indexes();

    /**
     * Returns index definition
     *
     * @param string $index
     *
     * @return IndexInterface
     * @throws ModelException
     */
    public function index($index);

    /**
     * Returns true if at last one relation is defined
     *
     * @return bool
     */
    public function hasRelations();

    /**
     * Returns true if relation to passed entity class is defined
     *
     * @param string $relationName
     *
     * @return bool
     */
    public function hasRelation($relationName);

    /**
     * Returns all relation definition
     *
     * @return array|RelationInterface[]
     */
    public function relations();

    /**
     * Returns relation definition for passed entity class
     *
     * @param string $relationName
     *
     * @return RelationInterface
     * @throws ModelException
     */
    public function relation($relationName);
}
