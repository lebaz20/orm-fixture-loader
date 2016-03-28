<?php

namespace FixtureLoader\Service\Query;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Query
 * 
 * Handles database queries related business
 * 
 * 
 * 
 * @property ObjectManager $entityManager
 * @property string $truncateQuery
 * 
 * @package fixtureLoader
 * @subpackage query
 */
class Query
{

    /**
     *
     * @var ObjectManager 
     */
    public $entityManager;

    /**
     * @var string
     */
    static private $truncateQuery;
    
    /**
     * Set needed properties
     * 
     * 
     * @access public
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get entity dependencies among other entities
     * 
     * @access public
     * @param object $entity
     * 
     * @return array entity dependencies
     */
    public function getEntityDependenciesClasses($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }
        $classMetadata = $this->entityManager->getClassMetadata($entity);
        $associationNames = $classMetadata->getAssociationNames();
        $dependencies = array();
        foreach ($associationNames as $associationName) {
            // association is many to one or one to one
            if ($classMetadata->isSingleValuedAssociation($associationName)) {
                $associationMapping = $classMetadata->getAssociationMapping($associationName);
                // no column correspond to association in Entity table or association is self referenced to current entity
                if (empty($associationMapping["joinColumns"]) || $associationMapping["sourceEntity"] == $associationMapping["targetEntity"]) {
                    continue;
                }
                $dependencies[] = $associationMapping["targetEntity"];
            }
        }
        return array_unique($dependencies);
    }

    /**
     * Get all entities
     * 
     * @access public
     * 
     * @return array entities
     */
    public function getAllEntities()
    {
        $entities = array();
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($allMetadata as $entityMetadata) {
            $entities[] = $entityMetadata->getName();
        }
        return $entities;
    }
    
    /**
     * Get entities dependencies
     * 
     * @access public
     * 
     * @return array entities dependencies
     */
    public function getEntitiesDependenciesClasses()
    {
        $entities = $this->getAllEntities();
        $entitiesDependencies = array();
        foreach($entities as $entity){
            $entitiesDependencies[$entity] = $this->getEntityDependenciesClasses($entity);
        }
        
        return $entitiesDependencies;
    }
    
    /**
     * Truncate all tables in database
     * 
     * @access public
     */
    public function truncateDatabase()
    {
        $connection = $this->entityManager->getConnection();
        if (empty(self::$truncateQuery)) {
            $schemaManager = $connection->getSchemaManager();
            $tables = $schemaManager->listTables();
            $query = '';

            $query .= 'set foreign_key_checks=0;';
            foreach ($tables as $table) {
                $name = $table->getName();
                $query .= 'DELETE FROM ' . $name . ';VACUUM;';
            }
            $query .= 'set foreign_key_checks=1;';
            self::$truncateQuery = $query;
        }
        $connection->executeQuery(self::$truncateQuery);
    }

}
