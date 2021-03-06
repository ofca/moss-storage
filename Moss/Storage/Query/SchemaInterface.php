<?php
namespace Moss\Storage\Query;


interface SchemaInterface {

    // Schema operations
    const OPERATION_CHECK = 'check';
    const OPERATION_CREATE = 'create';
    const OPERATION_ALTER = 'alter';
    const OPERATION_DROP = 'drop';

    /**
     * Sets query operation
     *
     * @param string        $operation
     * @param string|object $entity
     *
     * @return $this
     */
    public function operation($operation, $entity = null);

    /**
     * Executes query
     * After execution query is reset
     *
     * @return mixed|null|void
     */
    public function execute();

    /**
     * Returns current query string
     *
     * @return string
     */
    public function queryString();

    /**
     * Resets adapter
     *
     * @return $this
     */
    public function reset();
} 