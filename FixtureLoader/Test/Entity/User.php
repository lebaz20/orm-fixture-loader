<?php

namespace FixtureLoader\Test\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Entity
 * @ORM\Entity()
 * @ORM\Table(name="user")
 * 
 * 
 * @property int $id
 * @property string $username
 * @property string $password
 * @property array $roles
 * 
 * @package fixtureLoader
 * @subpackage entity
 */
class User
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    public $id;

    /**
     *
     * @ORM\Column(type="string" , unique=true)
     * @var string
     */
    public $username;

    /**
     *
     * @ORM\Column(type="string" , length =64)
     * @var string
     */
    public $password;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Users\Entity\Role")
     * @var array FixtureLoader/Test\Entity\Role
     */
    public $roles;

    /**
     * hash password
     * 
     * 
     * @access public
     * @param string $password
     * @return string hashed password
     */
    static public function hashPassword($password)
    {
        if (function_exists("password_hash")) {
            return password_hash($password, PASSWORD_BCRYPT);
        }
        else {
            return crypt($password);
        }
    }

    /**
     * verify submitted password matches the saved one
     * 
     * 
     * @access public
     * @param string $givenPassword
     * @param string $savedPassword hashed password
     * @return bool true if passwords mathced, false else
     */
    static public function verifyPassword($givenPassword, $savedPassword)
    {
        if (function_exists('password_verify')) {
            return password_verify($givenPassword, $savedPassword);
        }
        else {
            return crypt($givenPassword, $savedPassword) == $savedPassword;
        }
    }

    /**
     * Prepare user entity
     * 
     * 
     * @access public
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get id
     * 
     * 
     * @access public
     * @return int id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get password
     * 
     * 
     * @access public
     * @return string password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get roles
     * 
     * 
     * @access public
     * @return ArrayCollection FixtureLoader/Test\Entity\Role roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get username
     * 
     * 
     * @access public
     * @return string username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     * 
     * 
     * @access public
     * @param string $password
     * @return User current entity
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }


    /**
     * add role
     * 
     * 
     * @access public
     * @param FixtureLoader/Test/Entity/Role $role
     * @return User current entity
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
        return $this;
    }

    /**
     * Set roles
     * 
     * 
     * @access public
     * @param array $roles array of Users\Entity\Role instances or just ids
     * @return User current entity
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }


}
