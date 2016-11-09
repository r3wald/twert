<?php

namespace Twert\Controller;

use Silex\Application;
use Twig_Environment;

class IndexController implements ControllerInterface
{
    /**
     * @var Application
     */
    private $app;

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

        /* @var $twig Twig_Environment */
        $twig = $this->app['twig'];
        return $twig->render(
            'index.twig',
            array(
                'tweets' => array_map(
                    function ($entry) {
                        foreach ($entry['entities']['urls'] as $u) {
                            $link = sprintf('<a href="%s">%s</a>', $u['expanded_url'], $u['url']);
                            $entry['text2'] = str_replace($u['url'], $link, $entry['text']);
                            if ($entry['text']==$entry['text2']) {
                                var_dump($entry['text'], $u);
                            }
                        }
                        return $entry;
                    }, $result['statuses']
                )
            )
        );

    }
}
