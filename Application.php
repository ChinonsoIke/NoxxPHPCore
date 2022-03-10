<?php
namespace NoxxPHP\Core;

use Exception;

class Application
{
    public static string $ROOT_DIR;
    public static Application $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public ?Controller $controller= null;
    public Database $db;
    public ?DbModel $user=null;
    public View $view;
    public string $userClass;
    public string $layout= 'main';

    public function __construct(string $rootDir, array $config)
    {
        self::$ROOT_DIR= $rootDir;
        self::$app= $this;
        $this->userClass= $config['userClass'];
        $this->request= new Request();
        $this->response= new Response();
        $this->session= new Session();
        $this->view= new View();
        $this->router= new Router($this->request, $this->response);
        $this->db= new Database($config['db']); // pass in config variables for setting up db connection

        $primaryValue= $this->session->get('user');
        if($primaryValue){
            $this->user= $this->userClass::findOne(['id'=> $primaryValue]);
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController(Controller $controller)
    {
        $this->controller= $controller;
    }

    public function login(DbModel $user)
    {
        $this->user= $user;
        $primaryValue= $user->id;
        $this->session->set('user', $primaryValue);

        return true;
    }

    public function logout()
    {
        $this->user= null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    /**
     * run the application
     */
    public function run()
    {
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error',[
                'exception'=> $e,
            ]);
        }
    }
}