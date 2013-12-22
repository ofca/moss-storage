<?php
namespace moss\storage\builder;


interface QueryInterface
{
    // Query operations
    const OPERATION_SELECT = 'select';
    const OPERATION_INSERT = 'insert';
    const OPERATION_UPDATE = 'update';
    const OPERATION_DELETE = 'delete';
    const OPERATION_CLEAR = 'clear';

    // Aggregate methods
    const AGGREGATE_DISTINCT = 'distinct';
    const AGGREGATE_COUNT = 'count';
    const AGGREGATE_AVERAGE = 'average';
    const AGGREGATE_MAX = 'max';
    const AGGREGATE_MIN = 'min';
    const AGGREGATE_SUM = 'sum';

    // Join types
    const JOIN_INNER = 'inner';
    const JOIN_LEFT = 'left';
    const JOIN_RIGHT = 'right';

    // Comparison operator types
    const COMPARISON_EQUAL = '=';
    const COMPARISON_NOT_EQUAL = '!=';
    const COMPARISON_LESS = '<';
    const COMPARISON_GREATER = '>';
    const COMPARISON_LESS_EQUAL = '<=';
    const COMPARISON_GREATER_EQUAL = '>=';
    const COMPARISON_LIKE = 'like';
    const COMPARISON_REGEX = 'regex';

    // Logical operator types
    const LOGICAL_AND = 'and';
    const LOGICAL_OR = 'or';

    // Sorting methods
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    /**
     * Sets container name
     *
     * @param string $container
     * @param string $alias
     *
     * @return $this
     */
    public function container($container, $alias = null);

    /**
     * Sets operation for builder
     *
     * @param string $operation
     *
     * @return $this
     * @throws BuilderException
     */
    public function operation($operation);

    /**
     * Adds fields to query
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields);

    /**
     * Adds field to query
     *
     * @param string      $field
     * @param null|string $alias
     *
     * @return $this
     */
    public function field($field, $alias = null);

    /**
     * Adds distinct method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function distinct($field);

    /**
     * Adds count method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function count($field);

    /**
     * Adds average method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function average($field);

    /**
     * Adds max method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function max($field);

    /**
     * Adds min method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function min($field);

    /**
     * Adds sum method to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function sum($field);

    /**
     * Adds aggregate method to query
     *
     * @param string $method
     * @param string $field
     * @param string $alias
     *
     * @return $this
     * @throws BuilderException
     */
    public function aggregate($method, $field, $alias = null);

    /**
     * Adds grouping to query
     *
     * @param string $field
     *
     * @return $this
     */
    public function group($field);

    /**
     * Adds sub query

     *
*@param QueryInterface $query
     * @param string                $alias

     *
*@return $this
     */
    public function sub(QueryInterface $query, $alias);

    /**
     * Adds value to query
     *
     * @param string $col
     * @param mixed  $value
     *
     * @return $this
     */
    public function value($col, $value);

    /**
     * Adds inner join with set container
     *
     * @param string $container
     * @param array  $joins
     *
     * @return $this
     */
    public function innerJoin($container, array $joins);

    /**
     * Adds left join with set container
     *
     * @param string $container
     * @param array  $joins
     *
     * @return $this
     */
    public function leftJoin($container, array $joins);

    /**
     * Adds right join with set container
     *
     * @param string $container
     * @param array  $joins
     *
     * @return $this
     */
    public function rightJoin($container, array $joins);

    /**
     * Adds join to query
     *
     * @param string $type
     * @param array  $container
     * @param array  $joins
     *
     * @return $this
     * @throws BuilderException
     */
    public function join($type, $container, array $joins);

    /**
     * Adds condition to builder
     *
     * @param mixed  $field
     * @param mixed  $value
     * @param string $comp
     * @param string $log
     *
     * @return $this
     * @throws BuilderException
     */
    public function condition($field, $value, $comp = self::COMPARISON_EQUAL, $log = self::LOGICAL_AND);

    /**
     * Adds sorting to query
     *
     * @param string       $field
     * @param string|array $order
     *
     * @return $this
     * @throws BuilderException
     */
    public function order($field, $order = self::ORDER_DESC);

    /**
     * Sets limits to query
     *
     * @param int      $limit
     * @param null|int $offset
     *
     * @return $this
     */
    public function limit($limit, $offset = null);

    /**
     * Builds query string
     *
     * @return string
     */
    public function build();

    /**
     * Resets builder
     *
     * @return $this
     */
    public function reset();

    /**
     * Casts query to string (builds it)
     *
     * @return string
     */
    public function __toString();
} 