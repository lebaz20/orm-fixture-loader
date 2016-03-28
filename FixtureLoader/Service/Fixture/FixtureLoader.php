<?php

namespace FixtureLoader\Service\Fixture;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;

/**
 * FixtureLoader
 * 
 * Load fixtures needed in testing
 * 
 * @property FixtureLoader\Service\Query\Query $query
 * @property array $defaultFixtures
 * @property array $entitiesDependencies
 * @property bool $firstCallFlag ,default is true
 * 
 * @package fixtureLoader
 * @subpackage service
 */
class FixtureLoader
{

    /**
     *
     * @var FixtureLoader\Service\Query\Query 
     */
    public $query;

    /**
     *
     * @var array 
     */
    protected $defaultFixtures;

    /**
     *
     * @var array 
     */
    protected static $entitiesDependencies;

    /**
     * @var bool
     */
    static private $firstCallFlag = true;
    
    /**
     * Set needed properties
     * 
     * 
     * @access public
     * @param FixtureLoader\Service\Query\Query $query
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->setEntitiesDependencies();
    }

    /**
     * Set entities dependencies
     * 
     * @access public
     */
    public function setEntitiesDependencies()
    {
        if (empty(self::$entitiesDependencies)) {
            self::$entitiesDependencies = $this->query->getEntitiesDependenciesClasses();
        }
    }

    /**
     * Set default fixtures
     * 
     * @access public
     * @param array $fixtures array of fixture classes
     */
    public function setDefaultFixtures($fixtures)
    {
        $this->defaultFixtures = $fixtures;
    }

    /**
     * Load fixtures from entitis and their dependencies
     * 
     * @access public
     * @param array $entities array of entity classes
     * @param bool $prepareDBForFixturesFlag ,default is false
     */
    public function loadFixturesFromEntities($entities, $prepareDBForFixturesFlag = false)
    {
        $fixtures = $this->getFixtures($entities);
        $this->loadFixtures($fixtures, $prepareDBForFixturesFlag);
    }

    /**
     * Load fixtures in database
     * 
     * @access public
     * @param array $fixtures array of fixture classes
     * @param bool $prepareDBForFixturesFlag ,default is false
     */
    public function loadFixtures($fixtures, $prepareDBForFixturesFlag = false)
    {
        if($prepareDBForFixturesFlag === true){
            $this->prepareDBForFixtures();
        }
        $loader = new Loader();
        $allFixtures = array_unique(array_merge($this->defaultFixtures, $fixtures));
        $preparedFixtures = $this->getFixturesObjects($allFixtures);
        foreach ($preparedFixtures as $fixture) {
            if (is_object($fixture)) {
                $loader->addFixture($fixture);
            }
        }
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->query->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Prepare database for fixtures
     * 
     * @access public
     */
    public function prepareDBForFixtures()
    {
        // refresh DB structure
        if (self::$firstCallFlag === true) {
            shell_exec("bin/doctrine orm:schema-tool:drop --force; "
                    . "bin/doctrine orm:schema-tool:update --force;");
        }
        else {
            $this->query->truncateDatabase();
        }

        parent::setUp();
        self::$firstCallFlag = false;
    }

    /**
     * Get fixtures from entitis and their dependencies
     * 
     * @access private
     * @param array $entities array of entity classes
     * @param bool $loadDependenciesFlag ,default is true
     * 
     * @return array fixture classes array
     */
    private function getFixtures($entities, $loadDependenciesFlag = true)
    {
        $classes = array();
        foreach ($entities as $entity) {
            if (is_object($entity)) {
                $entity = get_class($entity);
            }
            $entityClasses = array($entity);
            if ($loadDependenciesFlag === true) {
                $dependenciesClasses = self::$entitiesDependencies[$entity];
                $entityClasses = array_merge($entityClasses, $dependenciesClasses);
            }

            foreach ($entityClasses as &$class) {
                $class = str_replace(/* $search = */ "\\Entity\\", /* $replace = */ "\\Fixture\\",$class);
            }
            $classes = array_merge($classes, $entityClasses);
        }
        return array_unique($classes);
    }

    /**
     * Get fixtures objects
     * 
     * @access private
     * @param array $fixtures array of fixture classes
     * @param array $fixtures array of fixture objects
     */
    private function getFixturesObjects($fixtures)
    {
        foreach ($fixtures as &$fixture) {
            if (is_string($fixture) && class_exists($fixture)) {
                $fixture = new $fixture;
            }
        }
        return $fixtures;
    }

}
