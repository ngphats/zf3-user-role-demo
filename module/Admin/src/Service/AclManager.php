<?php 

namespace Admin\Service;

use Zend\Permissions\Acl\Acl as AclPermission;

class AclManager 
{
	protected $authManager;

	protected $aclConfig;	

	protected $username;

	protected $role;

	protected $access;

	protected $acl;

	function __construct($authManager, $aclConfig) 
	{
		$this->authManager = $authManager;
		$this->aclConfig = $aclConfig;
	}

	public function setUserAccess() 
	{
		$data = $this->authManager->getUserAccess();

		$this->role = isset($data['role']) ? $data['role'] : 'guest';
		$this->access = isset($data['access']) ? $data['access'] : '';
		$this->username = $data['username'] ?? '';
		return $this;
	}

	public function getUserName() 
	{
		if (is_null($this->username)) {
			$this->setUserAccess();
		}
		
		return $this->username;
	}

	public function getRole()
	{
		if (is_null($this->role)) {
			$this->setUserAccess();
		}
		
		return $this->role;
	}

	public function getAccess() 
	{
		if (is_null($this->access)) {
			$this->setUserAccess();
		}

		return $this->access;
	}

	// set acl
	public function setAcl() 
	{
		$acl = new AclPermission;
		
		// Add resource
		if (!empty($this->aclConfig)) {
			foreach($this->aclConfig as $key => $value) {
				$acl->addResource($value['resource']);
			}
		}

		if (is_null($this->role)) {
			$this->setUserAccess();
		}			

		// Add permission
		switch ($this->getRole()) {
			case 'guest':
				$acl->addRole('guest');
				$acl->allow('guest','admin:verifycontroller');
				break;
			case 'admin':
				$acl->addRole('admin');
				$acl->allow('admin');
				break;
			default:
				// Add role
				$acl->addRole($this->getRole());

				// Set allow
				$acl->allow($this->getRole(),'admin:verifycontroller');
				$access = $this->getAccess();
				if (!empty($access)) :
					foreach($access as $key => $val) :
						if (!empty($val))
							$acl->allow($this->getRole(), $key, $val);
					endforeach;
				endif;
				break;
		}

		$this->acl = $acl;
		return $this;
	}

	// get acl
	public function getAcl() 
	{
		if (is_null($this->acl)) {
			$this->setAcl();
		}

		return $this->acl;
	}

	public function checkRole($controller, $action) 
	{
		if (is_null($this->acl)) {
			$this->setAcl();
		}	
		
		$role = $this->getRole();
		
		return $this->getAcl()->isAllowed($role, $controller, $action) ? true : false;
	}
}