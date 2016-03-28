<?php

namespace FixtureLoader\Test\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role Entity
 * @ORM\Entity()
 * @ORM\Table(name="role")
 * 
 * 
 * @property int $id
 * @property string $name
 * 
 * @package fixtureLoader
 * @subpackage entity
 */
class Role
{

    /**
     * Anonymous role
     */
    const ANONYMOUS_ROLE = "Anonymous";
    /**
     * User role
     */
    const USER_ROLE = "User";
    /**
     * Admin role
     */
    const ADMIN_ROLE = "Admin";
   

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    public $id;

    /**
     *
     * @ORM\Column(type="string")
     * @var string
     */
    public $name;

    /**
     * Gets the value of id.
     *
     * @return int
     * @access public
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     * @access public
     */
    public function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     * @access public
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param string $name the name
     *
     * @return self
     * @access public
     */
    public function setName( $name )
    {
        $this->name = $name;

        return $this;
    }   

}
