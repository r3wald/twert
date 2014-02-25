<?php

namespace Twert\Controller;

class IndexController implements ControllerInterface
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function indexAction()
    {
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $method = 'GET';
        $params = '?q=Berlin%20%23s5';

        /* @var $twitter \TwitterAPIExchange() */
        $twitter = $this->app['service.twitter'];
        $json = $twitter
            ->setGetField($params)
            ->buildOauth($url, $method)
            ->performRequest();
        $result = json_decode($json, true);
#        var_dump($result);exit;
        return $this->app['twig']->render('index.twig', array(
            'tweets'=>$result['statuses']
        ));

    }
}
