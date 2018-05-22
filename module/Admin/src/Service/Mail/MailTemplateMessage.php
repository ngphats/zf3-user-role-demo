<?php 

namespace Admin\Service\Mail;

class MailTemplateMessage 
{
	protected $renderer;

    public function setMailTemplate($template) 
    {
        // Php render
        $resolver   = new \Zend\View\Resolver\TemplateMapResolver();
        $resolver->setMap(array(
            'mailTemplate' => __DIR__ . '/../../../view/mail/'. $template .'.phtml'
        ));

        $this->renderer       = new \Zend\View\Renderer\PhpRenderer();
        $this->renderer->setResolver($resolver);  
        return $this;
    }	

    public function getMailTemplate() 
    {
    	if (null === $this->renderer) {
    		$this->setMailTemplate();
    	}
    	return $this->renderer;
    }

    public function message(array $data = []) 
    {
        $viewModel  = new \Zend\View\Model\ViewModel();
        $viewModel->setTemplate('mailTemplate')->setVariables($data);
     
        $bodyPart			= new \Zend\Mime\Message();
        $bodyMessage    	= new \Zend\Mime\Part($this->getMailTemplate()->render($viewModel));
        $bodyMessage->type  = 'text/html';
        $bodyPart->setParts(array($bodyMessage)); 
        return $bodyPart;
    }
}