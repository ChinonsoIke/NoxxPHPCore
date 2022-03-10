<?php

namespace NoxxPHP\Core;

class View
{
    public string $title= '';

    public function renderView($view, $params=[])
    {
        // place view content inside layout file and render
        $viewContent= $this->renderOnlyView($view, $params);
        $layoutContent= $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent($viewContent)
    {
        // place view content inside layout file and render
        $layoutContent= $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function layoutContent()
    {
        $layout= Application::$app->layout;
        if(Application::$app->controller){
            $layout= Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR."/views/layouts/{$layout}.php";
        return ob_get_clean();
    }

    public function renderOnlyView($view, $params=[])
    {
        foreach($params as $key=>$value){
            $$key= $value;
        }
        ob_start(); // output buffer
        include_once Application::$ROOT_DIR."/views/{$view}.php";
        return ob_get_clean();
    }
}