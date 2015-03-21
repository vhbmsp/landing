<?php

namespace Landing\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends BaseController
{

    public function indexAction(Request $request, Application $app, $keywords = "")
    {

        $contents = [
                    'myContent' => 'This is my content variable!'
                ];

        return $this->getRenderedContent($app, 'index.html.twig', $contents);
    }
}
