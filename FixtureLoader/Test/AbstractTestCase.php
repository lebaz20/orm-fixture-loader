<?php

namespace FixtureLoader\Test;

use FixtureLoader\Service\Fixture\FixtureLoader;
use FixtureLoader\Service\Query\Query;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * AbstractTestCase Parent for each and every test case providing common needs for all test cases
 * 
 * @property FixtureLoader\Service\Fixture\FixtureLoader $fixtureLoader
 * @property bool $firstTestCaseFlag ,default is true
 * @property string $truncateQuery
 * 
 * @package defaultModule
 * @subpackage test
 */
abstract class AbstractTestCase extends \PHPUnit_Extensions_PhptTestCase
{

    /**
     *
     * @var FixtureLoader\Service\Fixture\FixtureLoader
     */
    public $fixtureLoader;

    /**
     * Setup test case needed properties
     * 
     * @access public
     */
    public function setUp()
    {
        $entityManager = $this->getEntityManager();
        $query = new Query($entityManager);
        $this->fixtureLoader = new FixtureLoader($query);
        $this->fixtureLoader->setDefaultFixtures(array(
            "System\Fixture\Settings",
            "Users\Fixture\Acl",
            "Users\Fixture\Role"
        ));


        parent::setUp();
    }

    /**
     * Get entity manager
     * 
     * @access public
     * @return EntityManager
     */
    public function getEntityManager()
    {
        $paths = array("/Entity");
        $isDevMode = false;

        // the connection configuration
        $dbParams = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        return EntityManager::create($dbParams, $config);
    }

}
