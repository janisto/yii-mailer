<?php

/**
 * Class SwiftMailerComponent
 *
 * @uses CApplicationComponent
 * @version 1.0.0
 * @copyright 2013
 * @author Jani Mikkonen <janisto@php.net>
 * @license public domain
 */

class SwiftMailerComponent extends CApplicationComponent
{
	/**
	 * @var string $type swift mailer type: smtp, sendmail or mail
	 */
	public $type = 'mail';
	/**
	 * @var string $host host
	 */
	public $host = 'localhost';
	/**
	 * @var int $port port: 25, 465 or 587
	 */
	public $port = 25;
	/**
	 * @var string $username username
	 */
	public $username = null;
	/**
	 * @var string $password password
	 */
	public $password = null;
	/**
	 * @var string $security security: ssl, tls or null
	 */
	public $security = null;
	/**
	 * @var int $throttle number of emails per minute
	 */
	public $throttle = null;
	/**
	 * @var string $command sendmail command
	 */
	public $command = '/usr/sbin/sendmail -bs';
	/**
	 * @var Swift_Transport $transport
	 */
	private $transport;
	/**
	 * @var Swift_Mailer $mailer
	 */
	private $mailer;

	/**
	 * Get transport
	 *
	 * @return Swift_Transport
	 */
	private function getTransport()
	{
		if ($this->transport === null) {
			switch ($this->type) {
				case 'smtp':
					$this->transport = Swift_SmtpTransport::newInstance($this->host, $this->port, $this->security)
						->setUsername($this->username)
						->setPassword($this->password);
					break;
				case 'sendmail':
					$this->transport = Swift_SendmailTransport::newInstance($this->command);
					break;
				default:
					$this->transport = Swift_MailTransport::newInstance();
			}
		}

		return $this->transport;
	}

	/**
	 * Get mailer
	 *
	 * @return Swift_Mailer
	 */
	public function getMailer()
	{
		if ($this->mailer === null) {
			$this->mailer = Swift_Mailer::newInstance($this->getTransport());
			if (is_int($this->throttle)) {
				$this->mailer->registerPlugin(new Swift_Plugins_ThrottlerPlugin(
					$this->throttle, Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE
				));
			}
		}

		return $this->mailer;
	}

	/**
	 * Create message
	 *
	 * @param string $subject
	 * @param string $body
	 * @param string $contentType
	 * @param string $charset
	 *
	 * @return Swift_Message
	 */
	public function createMessage($subject = null, $body = null, $contentType = null, $charset = null)
	{
		return Swift_Message::newInstance($subject, $body, $contentType, $charset);
	}

	/**
	 * Send message
	 *
	 * @param Swift_Mime_Message $message
	 * @param array $failedRecipients An array of failures by-reference
	 *
	 * @return integer
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		return $this->getMailer()->send($message, $failedRecipients);
	}
}