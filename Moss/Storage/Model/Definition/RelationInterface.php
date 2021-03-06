<?php
namespace Moss\Storage\Model\Definition;

/**
 * Relation definition interface for entity model
 *
 * @package Moss storage
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface RelationInterface
{

    /**
     * Returns relation name in entity
     *
     * @return string
     */
    public function name();

    /**
     * Returns relation mediating instance
     *
     * @return string
     */
    public function mediator();

    /**
     * Returns relation type
     *
     * @return string
     */
    public function type();

    /**
     * Returns relation entity class name
     *
     * @return string
     */
    public function entity();

    /**
     * Returns table name
     *
     * @return string
     */
    public function container();

    /**
     * Returns associative array containing local key - foreign key pairs
     *
     * @return array
     */
    public function keys();

    /**
     * Returns array containing local keys
     *
     * @return array
     */
    public function localKeys();

    /**
     * Returns array containing foreign keys
     *
     * @return array
     */
    public function foreignKeys();

    /**
     * Returns associative array containing local key - value pairs
     *
     * @param array $localValues ;
     *
     * @return array
     */
    public function localValues($localValues = array());

    /**
     * Returns associative array containing foreign key - value pairs
     *
     * @param array $foreignValues ;
     *
     * @return array
     */
    public function foreignValues($foreignValues = array());
}
