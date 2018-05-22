<?php 
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Entity\User;
use Admin\Entity\Role;
use Admin\Form\RoleForm;

class RoleController extends AbstractActionController
{
	protected $aclConfig;

	protected $entityManager;

	function __construct($aclConfig, $entityManager) {
		$this->aclConfig = $aclConfig;
		$this->entityManager = $entityManager;
	}

	public function indexAction()
	{
		$em = $this->entityManager;
		$roles = $em->getRepository(Role::class)->findAll();

		return new ViewModel([
			'roles' => $roles
		]);
	}

	/**
	 * Create role
	 */
	public function createAction() 
	{
		$form = new RoleForm($this->aclConfig);
		
		return new ViewModel([
			'acl_config'	=> $this->aclConfig, 
			'form'			=> $form
 		]);
	}

	public function storeAction() 
	{
		$form = new RoleForm($this->aclConfig);
		$request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost()->toArray();
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) 
            {
            	$formData = $form->getData();

            	$role = new \Admin\Entity\Role();

            	$role->setName($formData['name']);
            	$role->setDescription($formData['description']); 	

            	foreach ($formData as $key => $val) {

            		if (!is_array($val)) {
            			unset($formData[$key]);
            		}
            	}

            	$access = json_encode($formData);
            	$role->setAccess($access);

				$this->entityManager->persist($role);
				$this->entityManager->flush();

				$idRole = $role->getId();
				if ($idRole) {
			        $this->flashMessenger()->addSuccessMessage('Create role successful.');
			        $this->redirect()->toRoute('admin/role', [
			        	'action' => 'edit',
			        	'id' => $idRole,
			        ]);
				}

            } else {
            	$view = new ViewModel([
            		'form'			=> $form,
            		'acl_config'	=> $this->aclConfig
            	]);
            	$view->setTemplate('admin/role/create');
            	return $view;
            }

		}
		return $this->getResponse();
	}	

	public function editAction() 
	{
		$id = $this->params()->fromRoute('id');
		if (! $id) {
			return $this->redirect()->toRoute('admin/role');
		}

		$form = new RoleForm($this->aclConfig);

		try {
			$role = $this->entityManager->getRepository(Role::class)
										->findOneBy(['id' => $id]);

			if (null === $role) {
				return $this->redirect()->toRoute('admin/role');
			}

			$data = [
				'name' => $role->getName(),
				'description' => $role->getDescription(),
			];

			$access = json_decode($role->getAccess(), true);
			if (!is_null($access)) {
				$data = array_merge($data, $access);
			}
			
			$form->setData($data);
			
		} catch (Exception $e) {
			return $this->redirect()->toRoute('admin/role');
		}

		return new ViewModel([
			'id'			=> $id,
			'form'			=> $form,
			'acl_config' 	=> $this->aclConfig
		]);
	}

	public function updateAction() 
	{
		$id = $this->params()->fromRoute('id');
		if (! $id) {
			return $this->redirect()->toRoute('admin/role');
		}

		// call form
		$form = new RoleForm($this->aclConfig);

		$request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost()->toArray();

            $form->setData($data);
            
            // Validate form
            if($form->isValid()) 
            {
            	$formData = $form->getData();

				$role = $this->entityManager->getRepository(Role::class)
											->findOneBy(['id' => $id]);

				if (null === $role) {
					return $this->redirect()->toRoute('admin/role');
				}

            	$role->setName($formData['name']);
            	$role->setDescription($formData['description']); 

            	foreach ($formData as $key => $val) {

            		if (!is_array($val)) {
            			unset($formData[$key]);
            		}
            	}

            	$access = json_encode($formData);
            	$role->setAccess($access);

				$this->entityManager->persist($role);
				$this->entityManager->flush();

		        $this->flashMessenger()->addSuccessMessage('Edit role successful.');
		        $this->redirect()->toRoute('admin/role', [
		        	'action' => 'edit',
		        	'acl_config' 	=> $this->aclConfig,
		        	'id' => $id,
		        ]);

            } else {
            	$view = new ViewModel([
            		'form'			=> $form,
            		'acl_config'	=> $this->aclConfig
            	]);
            	$view->setTemplate('admin/role/create');
            	return $view;
            }

		}
		return $this->getResponse();

	}

	public function deleteAction() 
	{
		$id = $this->params()->fromRoute('id');
		if (! $id) {
			return $this->redirect()->toRoute('admin/role');
		}

		$role = $this->entityManager->getRepository(Role::class)
									->findOneBy(['id' => $id]);

		if (! $role) {
			return $this->redirect()->toRoute('admin/role');
		}

		$this->entityManager->remove($role);
		$this->entityManager->flush();
        
        $this->flashMessenger()->addSuccessMessage('Delete role successful.');
        $this->redirect()->toRoute('admin/role');
    }
}