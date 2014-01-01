<?php
namespace moss\storage;

use moss\storage\builder\BuilderInterface;
use moss\storage\builder\QueryInterface;
use moss\storage\builder\SchemaInterface;
use moss\storage\driver\DriverInterface;
use moss\storage\model\ModelBag;
use moss\storage\model\ModelInterface;
use moss\storage\query\Query;
use moss\storage\query\Schema;

/**
 * Storage
 *
 * @package moss Storage
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Storage implements StorageInterface
{

    /** @var DriverInterface */
    protected $driver;

    /** @var BuilderInterface */
    protected $builders = array(
        'query' => null,
        'schema' => null
    );

    /** @var ModelBag */
    protected $models = array();

    /**
     * Constructor
     * If Modeler passed - modeler will be used to build models on the run when not present in storage
     *
     * @param DriverInterface    $driver
     * @param BuilderInterface[] $builders
     *
     * @throws StorageException
     */
    function __construct(DriverInterface $driver, array $builders)
    {
        $this->driver = & $driver;
        $this->models = new ModelBag();

        foreach ($builders as $builder) {
            if (!$builder instanceof BuilderInterface) {
                throw new StorageException('Builder must be an instance of BuilderInterface');
            }

            if ($builder instanceof QueryInterface) {
                $this->builders['query'] = & $builder;
            }

            if ($builder instanceof SchemaInterface) {
                $this->builders['schema'] = & $builder;
            }

            unset($builder);
        }
    }

    /**
     * Returns adapter instance for specified entity
     *
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Registers model into storage
     *
     * @param string         $alias
     * @param ModelInterface $model
     *
     * @return Storage
     */
    public function registerModel($alias, ModelInterface $model)
    {
        $this->models->set($model, $alias);

        return $this;
    }

    /**
     * Returns entity class name
     *
     * @param string|object $entity
     *
     * @return string
     */
    protected function getEntityClass($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        return ltrim($entity, '\\');
    }

    /**
     * Returns true if model exists
     *
     * @param string|object $entityClass
     *
     * @return bool
     */
    public function hasModel($entityClass)
    {
        return $this->models->has($entityClass);
    }

    /**
     * Returns model instance
     *
     * @param string|object $entityClass
     *
     * @return model\ModelInterface
     * @throws StorageException
     */
    public function getModel($entityClass)
    {
        return $this->models->get($entityClass);
    }

    /**
     * Returns all registered models
     *
     * @return array|ModelInterface[]
     */
    public function getModels()
    {
        return $this->models->all();
    }

    /**
     * Returns true if entity container exists
     *
     * @param string|object $entityClass
     *
     * @return Schema
     */
    public function check($entityClass)
    {
        return $this->schema(Schema::OPERATION_CHECK, $entityClass);
    }

    /**
     * Returns query creating entity container
     *
     * @param string|object $entityClass
     *
     * @return Schema
     */
    public function create($entityClass)
    {
        return $this->schema(Schema::OPERATION_CREATE, $entityClass);
    }

    /**
     * Returns query altering entity container
     *
     * @param string|object $entityClass
     *
     * @return Schema
     */
    public function alter($entityClass)
    {
        return $this->schema(Schema::OPERATION_ALTER, $entityClass);
    }

    /**
     * Returns query removing entity container
     *
     * @param string|object $entityClass
     *
     * @return Schema
     */
    public function drop($entityClass)
    {
        return $this->schema(Schema::OPERATION_DROP, $entityClass);
    }

    protected function schema($operation, $entity)
    {
        $schema = new Schema($this->driver, $this->builders['schema'], $this->models);
        $schema->operation($operation, $this->models->getEntityClass($entity));
        return $schema;
    }

    /**
     * Returns count query for passed entity class
     *
     * @param string $entityClass
     *
     * @return Query
     */
    public function count($entityClass)
    {
        return $this->query(Query::OPERATION_COUNT, $entityClass);
    }

    /**
     * Returns read single entity for passed entity class
     *
     * @param string $entityClass
     *
     * @return Query
     */
    public function readOne($entityClass)
    {
        return $this->query(Query::OPERATION_READ_ONE, $entityClass);
    }

    /**
     * Returns read query for passed entity class
     *
     * @param string $entityClass
     *
     * @return Query
     */
    public function read($entityClass)
    {
        return $this->query(Query::OPERATION_READ, $entityClass);
    }

    /**
     * Returns insert query for passed entity object or entity class
     *
     * @param string|object $entity
     *
     * @return Query
     */
    public function insert($entity)
    {
        return $this->query(Query::OPERATION_INSERT, $entity);
    }

    /**
     * Returns write query for passed entity object or entity class
     *
     * @param string|object $entity
     *
     * @return Query
     */
    public function write($entity)
    {
        return $this->query(Query::OPERATION_WRITE, $entity);
    }

    /**
     * Returns update query for passed entity object or entity class
     *
     * @param string|object $entity
     *
     * @return Query
     */
    public function update($entity)
    {
        return $this->query(Query::OPERATION_UPDATE, $entity);
    }

    /**
     * Returns delete query for passed entity object or entity class
     *
     * @param string|object $entity
     *
     * @return Query
     */
    public function delete($entity)
    {
        return $this->query(Query::OPERATION_DELETE, $entity);
    }

    /**
     * Returns clear query for passed entity class
     *
     * @param string $entityClass
     *
     * @return Query
     */
    public function clear($entityClass)
    {
        return $this->query(Query::OPERATION_CLEAR, $entityClass);
    }

    protected function query($operation, $entity)
    {
        $query = new Query($this->driver, $this->builders['query'], $this->models);
        $query->operation($operation, $entity);
        return $query;
    }

    /**
     * Starts transaction
     *
     * @return $this
     */
    public function transactionStart()
    {
        $this->driver->transactionStart();

        return $this;
    }

    /**
     * Commits transaction
     *
     * @return $this
     */
    public function transactionCommit()
    {
        $this->driver->transactionCommit();

        return $this;
    }

    /**
     * RollBacks transaction
     *
     * @return $this
     */
    public function transactionRollback()
    {
        $this->driver->transactionRollback();

        return $this;
    }

    /**
     * Returns true if in transaction
     *
     * @return bool
     */
    public function transactionCheck()
    {
        return $this->driver->transactionCheck();
    }
}
