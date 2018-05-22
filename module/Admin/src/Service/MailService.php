<?php 

namespace Admin\Service;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;

class MailService 
{
	protected $transport;

	protected $mail;

	public function setTransport() 
	{
        $options = new SmtpOptions([
            'name' => 'localhost',
            'host' => 'smtp.gmail.com',
            'port'     => 465,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => 'ngphats@gmail.com',
                'password' => 'qzaclnkvlwyxuxib',
                'ssl'      => 'ssl'
            ], 
        ]);

        $this->transport = new SmtpTransport;
        $this->transport->setOptions($options);
        return $this;
    }

    public function getTransport() 
    {
        if (null === $this->transport) {
            $this->setTransport();
        }
        return $this->transport;
    }

    public function setMail($data = null) 
    {
        //create and set email:
        $this->mail = new Mail\Message;
        $this->mail->setFrom('ngphats@gmail.com', 'Hosting');
        $this->mail->addTo('seohoaphat@gmail.com', 'Customer');
        $this->mail->setSubject("iMail");
        $this->mail->setBody($data['body']);
        return $this;
    }

    public function getMail() 
    {
        if (null === $this->mail) {
            $this->setMail();
        }
        return $this->mail; 
    }

    public function sendMail() 
    {
        $this->getTransport()->send($this->getMail());
	}
}