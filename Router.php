<?php
namespace NoxxPHP\Core;

use NoxxPHP\Core\Request;
use NoxxPHP\Core\Application;
use NoxxPHP\Core\Exceptions\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes= [];

    public function __construct(Request $request, Response $response)
    {
        $this->request= $request;
        $this->response= $response;
    }

    public function get($path, $callback)
    {
        // store routes
        $this->routes['get'][$path]= $callback;
    }

    public function post($path, $callback)
    {
        // store routes
        $this->routes['post'][$path]= $callback;
    }

    public function resolve()
    {
        // resolve route and call callback
        $path= $this->request->getPath();
        $method= $this->request->method();
        $callback= $this->routes[$method][$path] ?? false;
        if($callback === false){
            throw new NotFoundException();
        }

        // check if view name or callback was passed
        if(is_string($callback)){
            // if view name, render view
            return Application::$app->view->renderView($callback);
        }

        // else call callback
        if(is_array($callback)){
            /** @var \NoxxPHP\Core\Controller $controller */
            $controller= new $callback[0](); // eg new SiteController()
            Application::$app->controller= $controller;
            $controller->action= $callback[1];
            $callback[0]= $controller;

            foreach($controller->getMiddlewares() as $middleware){
                $middleware->execute();
            }

            // call the method and pass in request instance as arg
            return call_user_func($callback, $this->request, $this->response);
        }
    }
}