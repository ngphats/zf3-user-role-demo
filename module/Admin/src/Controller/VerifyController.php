<?php 

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Admin\Form\LoginForm;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver;
use Admin\Entity\User;

class VerifyController extends AbstractActionController 
{
	protected $authManager;

	protected $entityManager;

	protected $recapchaKey;

	protected $mailService;

	function __construct($authManager, $entityManager, $recapchaKey, $mailService) 
	{
		$this->authManager = $authManager;
		$this->entityManager = $entityManager;
		$this->recapchaKey = $recapchaKey;
		$this->mailService = $mailService;
	}

	public function indexAction() 
	{
		$user = $this->authManager->checkIdentity();

		if (!$user) {
			$this->redirect()->toRoute('admin/verify', [
				'action' => 'login'
			]);
		}

		return new ViewModel([
			'user' => $user
		]);
	}

	public function loginAction() 
	{
		$form = new LoginForm();

		$request = $this->getRequest();
		if ($request->isPost()) {
			
			$data = $request->getPost()->toArray();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();
                
                $result = $this->authManager->login($data['username'], 
                									md5($data['password']), 
                									$data['remember_me']);

				switch ($result->getCode()) {

				    case Result::FAILURE_IDENTITY_NOT_FOUND:
				        /** do stuff for nonexistent identity **/
				        echo current($result->getMessages());
				        break;

				    case Result::FAILURE_CREDENTIAL_INVALID:
				        /** do stuff for invalid credential **/
				        echo current($result->getMessages());
				        break;

				    case Result::SUCCESS:
				        /** do stuff for successful authentication **/
				        $this->flashMessenger()->addSuccessMessage(current($result->getMessages()));
				        $this->redirect()->toRoute('admin/verify');
				        break;

				    default:
				        /** do stuff for other failure **/
				        echo current($result->getMessages());
				        break;
				}   
			}
		}

		return new ViewModel([
			'form' => $form,
		]);
	}

	public function logoutAction()
	{
		$this->authManager->logout();
	    $this->flashMessenger()->addSuccessMessage('Logout successful!');
	    $this->redirect()->toRoute('admin/verify', [
	    	'action' => 'login'
	    ]);

	    return [];
	}

	/**
	 * forgot password
	 * @param $email
	 * @param $link admin/verify/active/email/ngphats@gmail.com/token/md5($password + keycode)
	 */
	public function forgotAction() 
	{
		// new form
		$form = new \Admin\Form\ForgotPasswordForm($this->entityManager, $this->recapchaKey);

		// request
		$request = $this->getRequest();
		if ($request->isPost()) {
			$data = $request->getPost()->toArray();

			$form->setData($data);
			
			if ($form->isValid()) {
				
				$data = $form->getData();

				$userRepository = $this->entityManager->getRepository(User::class);
				$user = $userRepository->findOneBy(['email' => $data['email']]);

				$token = md5($user->getPassword().'zf3');
				$email = $user->getEmail();

				$link = $_SERVER['SERVER_NAME'] 
						. $this->url()->fromRoute('admin/verify', ['action' => 'active']) 
						. "/email/$email/token/$token";

				// mailtemplate 
				$content = [
			        'username'  => $user->getUserName(),
			        'link'     => $link,
			    ];

			    // send email through event manager
		        $this->getEventManager()->trigger('send_mail', $this, $content);

			    $this->flashMessenger()->addSuccessMessage('One email already send to you, please check email and follow to. Thanks!');
			    return $this->redirect()->toRoute('admin/verify', [
			    	'action' => 'forgot'
			    ]);
			}
		}
		
		return new ViewModel([
			'form'		=> $form
		]);
	}

	public function activeAction() 
	{	
		// get params from route
		$email = $this->params()->fromRoute('email');
		$token = $this->params()->fromRoute('token');

		if (null === $email || null === $token) {
		    return $this->redirect()->toRoute('admin/verify', [
		    	'action' => 'login'
		    ]);
		}
		
		// get user by email
		$userRepository = $this->entityManager->getRepository(User::class);
		$user = $userRepository->findOneBy(['email' => $email]);

		$myToken = md5($user->getPassword().'zf3');

		if ($token !== $myToken) {
		    return $this->redirect()->toRoute('admin/verify', [
		    	'action' => 'login'
		    ]);
		}

		// form change password
		$form = new \Admin\Form\ChangePasswordForm();

		$request = $this->getRequest();
		if ($request->isPost()) {	
			$data = $request->getPost()->toArray();
			$form = new \Admin\Form\ChangePasswordForm();
			$form->setData($data);
			if ($form->isValid()) {
				$data = $form->getData();
				$user->setPassword(md5($data['password']));
				$this->entityManager->persist($user);
				$this->entityManager->flush();
			    $this->flashMessenger()->addSuccessMessage('Change password successful!');
			    return $this->redirect()->toRoute('admin/verify', [
			    	'action' => 'login'
			    ]);
			}
		}

		return new ViewModel([
			'form' => $form,
			'email' => $email,
			'token' => $token
		]);
	}

	public function deniedAction() 
	{
		return new ViewModel();
	}
}
