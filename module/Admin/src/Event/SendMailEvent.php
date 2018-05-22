<?php 

namespace Admin\Event;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Admin\Service\Mail\MailTemplateMessage;
use Admin\Service\Mail\MailService;

class SendMailEvent implements ListenerAggregateInterface
{
	use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 100)
    {
    	$shared  		   = $events->getSharedManager();
        $this->listeners[] = $shared->attach('Admin', 'send_mail', [$this, 'onSendMail'], $priority);
    }

    public function onSendMail(EventInterface $e) 
    {
    	// set email template
		$mailTemplate = new MailTemplateMessage();
		$mailTemplate->setMailTemplate('forgotpassword');
		$data['body'] = $mailTemplate->message($e->getParams());

		// mailmessage
		$mailMessage = new MailService();
		$mailMessage->setMail($data)->sendMail();
    }
}
