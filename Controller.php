<?php

namespace NoxxPHP\Core;

use NoxxPHP\Core\Middlewares\BaseMiddleware;

class Controller
{
    public $layout= 'main';
    public string $action= '';

    /**
     * @var \NoxxPHP\Core\Middleware\BaseMiddleware[]
     */
    protected array $middlewares= [];

    // we can switch between different layout files
    public function setLayout($layout)
    {
        $this->layout= $layout;
    }

    // render the view
    public function render($view, $params=[ ])
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[]= $middleware;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}