<?php
namespace moss\storage\builder\mysql;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \moss\storage\builder\BuilderException
     * @expectedExceptionMessage Missing container name
     */
    public function testMissingContainer()
    {
        $schema = new Schema(null);
        $schema->build();
    }

    public function testContainer()
    {
        $schema = new Schema('table', Schema::OPERATION_ADD);
        $schema->column('foo');
        $this->assertEquals('ALTER TABLE `table` ADD `foo` TEXT NOT NULL', $schema->build());
    }

    /**
     * @expectedException \moss\storage\builder\BuilderException
     * @expectedExceptionMessage Unknown operation
     */
    public function testInvalidOperation()
    {
        new Schema('table', 'foo');
    }


    /**
     * @dataProvider shortOperationProvider
     */
    public function testShortOperation($operation, $expected)
    {
        $schema = new Schema('table', $operation);
        $schema->column('foo')
               ->index('idx', array('foo'), 'index');
        $this->assertEquals($expected, $schema->build());
    }

    public function shortOperationProvider()
    {
        return array(
            array(
                Schema::OPERATION_CHECK,
                'SHOW TABLES LIKE \'table\''
            ),
            array(
                Schema::OPERATION_INFO,
                'SHOW CREATE TABLE `table`'
            ),
            array(
                Schema::OPERATION_DROP,
                'DROP TABLE IF EXISTS `table`'
            )
        );
    }

    /**
     * @dataProvider columnProvider
     */
    public function testCreateColumn($actual, $expected)
    {
        $schema = new Schema('table', Schema::OPERATION_CREATE);
        $schema->column('foo', $actual);
        $this->assertEquals('CREATE TABLE `table` ( `foo` '.$expected.' ) ENGINE=InnoDB DEFAULT CHARSET=utf8', $schema->build());
    }

    /**
     * @dataProvider columnProvider
     */
    public function testAddColumn($actual, $expected)
    {
        $schema = new Schema('table', Schema::OPERATION_ADD);
        $schema->column('foo', $actual);
        $this->assertEquals('ALTER TABLE `table` ADD `foo` '.$expected.'', $schema->build());
    }

    /**
     * @dataProvider columnProvider
     */
    public function testChangeColumn($actual, $expected)
    {
        $schema = new Schema('table', Schema::OPERATION_CHANGE);
        $schema->column('foo', $actual);
        $this->assertEquals('ALTER TABLE `table` CHANGE `foo` `foo` '.$expected.'', $schema->build());
    }

    /**
     * @dataProvider columnProvider
     */
    public function testRemoveColumn($actual, $expected)
    {
        $schema = new Schema('table', Schema::OPERATION_REMOVE);
        $schema->column('foo', $actual);
        $this->assertEquals('ALTER TABLE `table` DROP `foo`', $schema->build());
    }

    public function columnProvider()
    {
        return array(
            array(
                Schema::FIELD_BOOLEAN,
                'TINYINT(1) COMMENT \'boolean\' NOT NULL'
            ),
            array(
                Schema::FIELD_INTEGER,
                'INT(10) NOT NULL',
            ),
            array(
                Schema::FIELD_DECIMAL,
                'DECIMAL(10,0) NOT NULL',
            ),
            array(
                Schema::FIELD_STRING,
                'TEXT NOT NULL'
            ),
            array(
                Schema::FIELD_DATETIME,
                'DATETIME NOT NULL'
            ),
            array(
                Schema::FIELD_SERIAL,
                'TEXT COMMENT \'serial\' NOT NULL'
            ),

        );
    }

    /**
     * @dataProvider attributeProvider
     */
    public function testColumnAttributes($type, $attributes, $expected)
    {
        $schema = new Schema('table', Schema::OPERATION_ADD);
        $schema->column('foo', $type, $attributes);
        $this->assertEquals('ALTER TABLE `table` ADD `foo` '.$expected.'', $schema->build());
    }

    public function attributeProvider()
    {
        return array(
            array(
                Schema::FIELD_INTEGER,
                array('unsigned'),
                'INT(10) UNSIGNED NOT NULL'
            ),
            array(
                Schema::FIELD_INTEGER,
                array('default' => 1),
                'INT(10) DEFAULT 1'
            ),
            array(
                Schema::FIELD_INTEGER,
                array('auto_increment'),
                'INT(10) NOT NULL AUTO_INCREMENT'
            ),
            array(
                Schema::FIELD_INTEGER,
                array('null'),
                'INT(10) DEFAULT NULL'
            ),
            array(
                Schema::FIELD_INTEGER,
                array('length' => 6),
                'INT(6) NOT NULL'
            ),
            array(
                Schema::FIELD_STRING,
                array('length' => null),
                'TEXT NOT NULL'
            ),
            array(
                Schema::FIELD_STRING,
                array('length' => 2048),
                'TEXT NOT NULL'
            ),
            array(
                Schema::FIELD_STRING,
                array('length' => 512),
                'VARCHAR(512) NOT NULL'
            ),
            array(
                Schema::FIELD_STRING,
                array('length' => 10),
                'CHAR(10) NOT NULL'
            ),
            array(
                Schema::FIELD_DECIMAL,
                array('precision' => 2),
                'DECIMAL(10,2) NOT NULL'
            ),
            array(
                Schema::FIELD_DECIMAL,
                array('length' => 6, 'precision' => 2),
                'DECIMAL(6,2) NOT NULL'
            ),
            array(
                Schema::FIELD_INTEGER,
                array('comment' => 'some comment'),
                'INT(10) COMMENT \'some comment\' NOT NULL'
            ),
        );
    }

    /**
     * @dataProvider indexProvider
     */
    public function testCreateIndex($actual, $expected)
    {
        $this->markTestIncomplete();
        $schema = new Schema('table', Schema::OPERATION_CREATE);
        $schema->column('foo', $actual);
        $this->assertEquals('CREATE TABLE `table` ( `foo` '.$expected.' ) ENGINE=InnoDB DEFAULT CHARSET=utf8', $schema->build());
    }

    /**
     * @dataProvider indexProvider
     */
    public function testAddIndex($actual, $expected)
    {
        $this->markTestIncomplete();
        $schema = new Schema('table', Schema::OPERATION_ADD);
        $schema->column('foo', $actual);
        $this->assertEquals('ALTER TABLE `table` ADD `foo` '.$expected.'', $schema->build());
    }


    public function indexProvider()
    {
        return array(
            array(
                Schema::INDEX_PRIMARY,
                'TINYINT(1) COMMENT \'boolean\' NOT NULL'
            ),
            array(
                Schema::INDEX_UNIQUE,
                'INT(10) NOT NULL',
            ),
            array(
                Schema::INDEX_INDEX,
                'DECIMAL(10,0) NOT NULL',
            ),
            array(
                Schema::INDEX_FOREIGN,
                'TEXT NOT NULL'
            ),
        );
    }

    /**
     * @dataProvider dropIndexProvider
     */
    public function testRemoveIndex($actual, $expected)
    {
        $this->markTestIncomplete();
        $schema = new Schema('table', Schema::OPERATION_REMOVE);
        $schema->column('foo', $actual);
        $this->assertEquals('ALTER TABLE `table` DROP `foo`', $schema->build());
    }

    public function dropIndexProvider()
    {
        return array(
            array(
                Schema::INDEX_PRIMARY,
                'TINYINT(1) COMMENT \'boolean\' NOT NULL'
            ),
            array(
                Schema::INDEX_UNIQUE,
                'INT(10) NOT NULL',
            ),
            array(
                Schema::INDEX_INDEX,
                'DECIMAL(10,0) NOT NULL',
            ),
            array(
                Schema::INDEX_FOREIGN,
                'TEXT NOT NULL'
            ),
        );
    }
}