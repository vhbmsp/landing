<?php

namespace Landing\Controller;

use Landing\Model;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BaseController
{

    protected function getRenderedContent(Application $app, $template, $controllerContents)
    {

        // Common templates contents
        $generalContents = [
                'myCommonContents' => 'My Common Contents with concat',
            ];

        // Merge generalContents with controllerContents
        $outputContent = array_merge($generalContents, $controllerContents);

        // Render Twig template response
        return $app['twig']->render(
            $template,
            $outputContent
        );

    }
}
