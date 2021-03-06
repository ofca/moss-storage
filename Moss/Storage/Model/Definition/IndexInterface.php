<?php
namespace Moss\Storage\Model\Definition;

/**
 * Interface for index definition for entity model
 *
 * @package Moss storage
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
interface IndexInterface
{
    /**
     * Returns table that field belongs to
     *
     * @return string
     */
    public function table();

    /**
     * Returns relation name in entity
     *
     * @return string
     */
    public function name();

    /**
     * Returns relation type
     *
     * @return string
     */
    public function type();

    /**
     * Returns array containing field names (unmapped) that are included in index
     *
     * @return array
     */
    public function fields();

    /**
     * Checks if index uses passed field (unmapped)
     * Returns true if it does
     *
     * @param string $field
     *
     * @return bool
     */
    public function hasField($field);

    /**
     * Returns true if index is primary index
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Returns true if index is unique
     *
     * @return bool
     */
    public function isUnique();
}
