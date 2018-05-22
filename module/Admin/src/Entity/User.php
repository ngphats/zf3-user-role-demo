<?php

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
* @ORM\Entity
* @ORM\Table(name="users")
*/
class User
{
	const ACTIVE = 1;
	
	/**
	* @ORM\Id
	* @ORM\Column(name="id")
	* @ORM\GeneratedValue
	*/
	protected $id;
	
	/**
	* @ORM\Column(name="username")
	*/
	protected $username;

	/**
	* @ORM\Column(name="password")
	*/
	protected $password;

	/**
	 * @ORM\Column(type="integer", name="phone", unique=false, nullable=true)
	 */
	protected $phone = null;
	
	/**
	* @ORM\Column(name="email")
	*/
	protected $email;

	/**
	 * @ORM\Column(type="string", name="address", unique=false, nullable=true)
	 */
	protected $address = null;

	/**
	 * @ORM\Column(type="string", name="avatar", unique=false, nullable=true)
	 */
	protected $avatar = null;

	/**
	* @ORM\Column(name="level")
	*/
	protected $level;

	/**
	 * @ORM\Column(type="integer", name="active", unique=false, nullable=true)
	 */
	protected $active = null;

	/**
	 * @ORM\ManyToOne(targetEntity="Role")
	 * @ORM\JoinColumn(name="level", referencedColumnName="id")
	 */
	protected $role;

	function __construct() {
		$this->role = new ArrayCollection();
	}
	
	/** Set and get Id */
	public function setId($id){
		$this->id=$id;
	}
	public function getId(){
		return $this->id;
	}

	/** Set and get Username */
	public function setUsername($user){
		$this->username=$user;
	}
	public function getUsername(){
		return $this->username;
	}		
	
	/** Set and get Password */
	public function setPassword($pass){
		$this->password=$pass;
	}
	public function getPassword(){
		return $this->password;
	}

	/** Set and get Email */
	public function setEmail($email){
		$this->email=$email;
	}
	public function getEmail(){
		return $this->email;
	}

	/** Set and get Level */
	public function setLevel($lv){
		$this->level=$lv;
	}
	public function getLevel(){
		return $this->level;
	}

	/** Set and get Phone */
	public function setPhone($phone) {
		$this->phone = $phone;
	}
	public function getPhone() {
		return $this->phone;
	}
	
	/** Set and get Avatar */
	public function setAvatar($avatar) {
		$this->avatar = $avatar;
	}
	public function getAvatar() {
		return $this->avatar;
	}

	/** Set and get Address */
	public function setAddress($address) {
		$this->address = $address;
	}
	public function getAddress() {
		return $this->address;
	}

	/** Set and get Active */
	public function setActive($active) {
		$this->active = $active;
	}
	public function getActive() {
		return $this->active;
	}

	// Set and get Role
	public function setRole($role) {
		$this->role = $role;
	} 
	public function getRole() {
		return $this->role;
	}
}