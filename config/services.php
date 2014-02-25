<?php

/* @var $app \Silex\Application */

use Monolog\Handler\RotatingFileHandler;
use Silex\Provider\SessionServiceProvider;

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../templates',
));

$app->register(
    new Silex\Provider\MonologServiceProvider(),
    array(
        'monolog.handler' => new RotatingFileHandler(__DIR__ . '/../var/log/app.log')
    )
);

/****************************************/

$pdoUsermanagement = new PDO(
    $app['config']['database.dsn'],
    $app['config']['database.username'],
    $app['config']['database.password'],
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    )
);
$fluentPdoUsermanagement = new FluentPDO($pdoUsermanagement);

/****************************************/

$app->register(new SessionServiceProvider(),array(

));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'http' => true,
            'users' => array(
                // raw password is foo
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        )
    )
));

/****************************************/

$app['service.twitter'] = new TwitterAPIExchange($app['config']['twitter']);

/****************************************/

/*$mailTransport = \Swift_SmtpTransport::newInstance($app['config']['mail.smtphost']);
$mailMailer = new \Swift_Mailer($mailTransport);
$mailOptions = array();
if (!empty($app['config']['debug.email_override'])) {
    $mailOptions['override_email'] = $app['config']['debug.email_override'];
}
if (!empty($app['config']['debug.email_bcc'])) {
    $mailOptions['_bcc_recipients'] = $app['config']['debug.email_bcc'];
}
$app['service.mail'] = $mailTransport;
*/

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['controller.index'] = $app->share(function () use ($app) {
    return new \Twert\Controller\IndexController($app);
});

$app->get('/', "controller.index:indexAction");