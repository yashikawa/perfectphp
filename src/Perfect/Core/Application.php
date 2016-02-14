<?php

namespace Perfect\Core;

use Perfect\Core\Exception\HttpNotFoundException;
use Perfect\Core\Exception\UnauthorizedActionException;

abstract class Application
{

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DbManager
     */
    protected $db_manager;

    /**
     * @var Router
     */
    protected $router;

    protected $login_actions = [];

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($debug)
    {
        $this->debug = $debug;
    }

    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {
        // For override.
    }

    abstract public function getRootDir();

    abstract protected function registerRoutes();

    /**
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return mixed
     */
    public function getDbManager()
    {
        return $this->db_manager;
    }

    /**
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/Controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/Views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/Models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/Web';
    }

    public function run()
    {
        try {
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller_name = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller_name, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            list ($controller, $action) = $this->login_actions;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }

    public function runAction($controller_name, $action, $params = [])
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);

        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' controller is not found.');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    protected function findController($controller_class)
    {
        $controller_class = str_replace('/', '\\', $this->getControllerDir() . '/' . $controller_class);
        if (!class_exists($controller_class)) {
            return false;
        }

        return new $controller_class($this);
    }

    /**
     * @param HttpNotFoundException $e
     */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>404</title>
</head>
<body>
    {$message}
</body>
</html>
EOF
        );
    }
}