<?php 
namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="roles")
 */
class Role 
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id = null;
	
	/**
	 * @ORM\Column(type="string", name="name", unique=false, nullable=false)
	 */
	protected $name = null;
	
	/**
	 * @ORM\Column(type="text", name="access")
	 */
	protected $access = '';

	/**
	 * @ORM\Column(type="text", name="description")
	 */
	protected $description = '';

	/**
	 * @ORM\OneToMany(targetEntity="User", mappedBy="role", cascade={"persist","remove"}, orphanRemoval=true)
	 */
	protected $users;
	
	function __construct() {
		$this->user = new ArrayCollection();
	}
	
	// Set and get Id
	public function setId($id) {
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}

	// Set and get name
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}

	// Set and get access
	public function setAccess($access) {
		$this->access = $access;
	}
	public function getAccess() {
		return $this->access;
	}

	// Set and get description
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}

	// Set and get role
	public function setUser($user) {
		$this->users = $user;
	}
	public function getUser() {
		return $this->$users;
	}
}