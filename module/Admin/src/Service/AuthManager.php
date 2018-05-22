<?php 

namespace Admin\Service;

use Admin\Entity\Role;
use Zend\Authentication\Result;

class AuthManager 
{
	protected $authService;

	protected $sessionManager;

	protected $entityManager;

	function __construct($authService, $sessionManager, $entityManager) 
	{
		$this->authService = $authService;
		$this->sessionManager = $sessionManager;
		$this->entityManager = $entityManager;
	}

	public function checkIdentity() 
	{
		if ($this->authService->hasIdentity()) {
			$user = $this->authService->getIdentity();
			return $user;
		} 
		return false;
	}

	public function getUserAccess() 
	{
		if ($this->authService->hasIdentity()) {
			$user = $this->authService->getIdentity();
			$roleRepository = $this->entityManager->getRepository(Role::class);
			$role = $roleRepository->findOneBy(['id' => $user['level']]);

			$data = [
				'username' => $user['username'],
				'role' => $role->getName(),
				'access' => json_decode($role->getAccess(), true)
			];

			return $data;
		}
	}

	public function login($username, $password, $remember = 0)
	{
		if ($this->authService->hasIdentity()) {
			$this->authService->clearIdentity();
		} 
				
		$this->authService->getAdapter()
						->setIdentity($username)
						->setCredential($password);
		$result = $this->authService->authenticate();

		if ($result->getCode() == Result::SUCCESS && $remember == 1) {
			$this->sessionManager->rememberMe(60*60*24*30);
		}

		return $result;
	} 

	public function logout()
	{
		if ($this->authService->hasIdentity()) {
			$this->authService->clearIdentity();
		}
	}
}