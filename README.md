yii-mailer
==========

Yii Component for Swift Mailer

- [Documentation](http://swiftmailer.org/docs/messages.html)
- [Github Project Page](https://github.com/janisto/yii-mailer/)

Requirements
------------------

- Yii 1.1.14 or above
- [Composer](http://getcomposer.org/doc/)

Installation
------------------

- Require the package.

~~~
{
	"name": "app-name",
	"description": "App description",
	"type": "project",
	"prefer-stable": true,
	"require": {
		"php": ">=5.3.0",
		"yiisoft/yii": "1.1.14",
		"janisto/yii-mailer": "1.0.0"
	}
}
~~~

- Include Composer autoloader before Yii in your entry script (index.php and/or yiic.php for console scripts).

~~~
// Composer autoload
$composerAutoload = dirname(__FILE__) . '/../vendor/autoload.php';
require_once($composerAutoload);
...
~~~

- Add vendor path to your configuration file, attach component and set properties.

~~~
	'aliases'=>array(
		'vendor' => realpath(__DIR__ . '/../../vendor'),
	),
	'components' => array(
		...
		'mailer' => array(
			'class' => 'vendor.janisto.yii-mailer.SwiftMailerComponent',
			'type' => 'smtp',
			'host' => 'email-smtp.us-east-1.amazonaws.com',
			'port' => 587,
			'username' => 'xxx',
			'password' => 'yyy',
			'security' => 'tls',
			'throttle' => 5*60,
		),
		...
	),
~~~

Usage
------------------

~~~
$message = Yii::app()->mailer
	->createMessage('Your subject', 'Here is the message itself')
	->setFrom(array('from@domain.com' => 'From Name'))
	->setTo(array('to@domain.com' => 'To Name'));

Yii::app()->mailer->send($message);
~~~

or

~~~
$failures = array();
$sent = 0;
$from = array('from@domain.com' => 'From Name');
$emails = array(
	array('to@domain.com' => 'To Name'),
	array('receiver@bad-domain.org' => 'To Name'),
	array('other-receiver@bad-domain.org' => 'To Name'),
);

/* @var Swift_Message $message */
$message = Yii::app()->mailer
	->createMessage('Your subject')
	->setFrom($from)
	->setBody('Here is the message itself')
	->addPart('<q>Here is the message itself</q>', 'text/html');

foreach ($emails as $to) {
	$message->setTo($to);
	try {
		$sent += Yii::app()->mailer->send($message, $failures);
	} catch (Exception $e) {
		// SMTP server not responding or limit exceeded?
		echo $e->getMessage();
	}
}

echo "$sent emails sent.\n";
echo "Failures:\n";
print_r($failures);
~~~

Changelog
---------

### v1.0.0

- Initial version.

License
-------

yii-mailer is free and unencumbered [public domain][Unlicense] software.

[Unlicense]: http://unlicense.org/