<?php

    class Midnight
    {
        private $_route     = [];
        private $_registry  = [];
        private $_namespace = '\\';
        private $_routed    = false;

        public function __construct()
        {
            $autoloaderPath = __DIR__ . DS . 'Autoloader.php';

            if (file_exists($autoloaderPath))
            {
                require_once($autoloaderPath);

                if (function_exists('__autoload'))
                {
                    spl_autoload_register('__autoload');
                }

                spl_autoload_register(['MN_Autoloader', 'autoload']);
            }

            MN_App::discover();
        }

        public function __toString()
        {
            return 'Midnight-v2.0b';
        }

        public function __get($name)
        {
            $return = null;
            $class  = 'MN_' . $name;

            if (class_exists($class))
            {
                if (!array_key_exists($class, $this->_registry))
                {
                    $this->_registry[$class] = new $class;
                }

                $return = $this->_registry[$class];

            } else {

                // TODO: error
            }

            return $return;
        }

        public function init()
        {
            if (!$this->_routed)
            {
                $_request = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));

                if (is_null(MN_App::loaded()))
                {
                    $apps = MN_Directory::scan(getcwd() . DS . 'apps');

                    if (count($apps) == 0)
                    {
                        throw new exception ('no apps detected');
                    }

                    if (count($apps) == 1)
                    {
                        $this->app($apps[0]['name']);
                    }

                    if (count($apps) > 1)
                    {
                        throw new exception ('no default app detected');
                    }
                }

                $this->route($_SERVER['REQUEST_URI']);

                if (count($_request) == 0)
                {
                    $this->to('index', 'index');
                }

                if (count($_request) == 1)
                {
                    $this->to($_request[0] == '/' ? 'index' : $_request[0], 'index');
                }

                if (count($_request) == 2)
                {
                    $this->to($_request[0], $_request[1]);
                }

                if (count($_request) >= 3)
                {
                    $this->to(array_shift($_request), array_shift($_request), $_request);
                }
            }
        }

        /**
         * @param $app string
         * @throws exception
         * @return object
         */
        public function app($app)
        {
            if (is_dir(getcwd() . DS . 'apps' . DS . $app))
            {
                MN_App::set($app);

                MN_Autoloader::register(getcwd() . DS . 'apps' . DS . $app);

            } else {

                throw new exception ('app does not exist');
            }

            return $this;
        }

        /**
         * @param $route string
         * @throws exception
         * @return object
         */
        public function route($route)
        {
            if (!is_null(MN_App::loaded())) {

                $this->_route = ['string' => $route];

            } else {

                throw new exception ('no app loaded');
            }

            return $this;
        }

        /**
         * @param $controller string
         * @param $action string|array
         * @param $arguments array
         * @throws exception
         * @return object
         */
        public function to($controller = 'index', $action = 'index', $arguments = [])
        {
            if ($this->_routed === false && isset($this->_route['string']))
            {
                $controller = strtolower($controller);

                if (strpos($this->_route['string'], '*') !== false)
                {
                    if (preg_match('#(' . str_replace('*', '.*', $this->_route['string']) . ')#i', $_SERVER['REQUEST_URI']))
                    {
                        $this->_routed = true;
                    }

                } else {

                    if ($this->_route['string'] == $_SERVER['REQUEST_URI'])
                    {
                        $this->_routed = true;
                    }
                }

                if ($this->_routed === true)
                {
                    $this->_route += $this->_route($controller, $action, $arguments);

                    $stack  = [];

                    if (!isset($stack['controller']))
                    {
                        $stack['controller']['invoke'] = $this->_namespace . $this->_route['controller']['translated'];
                    }

                    if (!isset($stack['action']))
                    {
                        $stack['action']['invoke'] = $this->_route['action']['translated'];
                    }

                    $c = str_replace('\\\\', '\\', $stack['controller']['invoke']);

                    if (class_exists($c))
                    {
                        $c = new $c();
                        $m = $stack['action']['invoke'];
                        $a = $this->_route['params'];

                        $j = method_exists($c, $m);
                        $i = is_callable([$c, $m]);
                        $n = method_exists($c, '__call');
                        $x = is_callable([$c, '__call']);

                        if (($j && $i) || ($n && $x))
                        {
                            if (count($a) == 3)
                            {
                                $c->$m($a[0], $a[1], $a[2]);

                            } else if (count($a) == 2) {

                                $c->$m($a[0], $a[1]);

                            } else if (count($a) == 1) {

                                $c->$m($a[0]);

                            } else if (count($a) == 0) {

                                $c->$m();

                            } else {

                                call_user_func_array([$c, $m], $a);
                            }
                        }

                    } else {

                        echo '404 error, class ' . $c . ' does not exist.';
                    }
                }
            }

            return $this;
        }

        private function _route($controller, $action = null, $arguments = [])
        {
            if (is_array($action))
            {
                $arguments = $action;
                $action    = 'index';
            }

            if (is_null($action) && empty($arguments))
            {
                $route = '/' . ltrim($controller, '/');

            } else {
                $route = '/' . $controller . '/' . $action . '/' . implode('/', $arguments);
            }

            $_route   = explode('/', $route);
            $params   = array_values(array_filter($_route));
            $_project = str_replace($_SERVER['DOCUMENT_ROOT'], '', getcwd());

            if ($params[0] == $_project)
            {
                array_shift($params);
            }

            if (!empty($params))
            {
                if ($params[0] != '-')
                {
                    $prefix = is_numeric($params[0][0]) ? 'n' : null;
                    $prefix = $params[0][0] == '_' ? 'u' : $prefix;
                    $prefix = $params[0][0] == '-' ? 'd' : $prefix;

                    $controller = array_shift($params);

                    $_r['controller']['raw']        = $controller;
                    $_r['controller']['translated'] = $prefix . str_replace('-', '_', $controller) . '_Controller';

                } else {

                    array_shift($params);

                    $_r['controller']['raw']        = 'index';
                    $_r['controller']['translated'] = 'Index_Controller';
                }

            } else {

                $_r['controller'] = ['raw' => 'index', 'translated' => 'Index_Controller'];
            }

            if (!empty($params))
            {
                if ($params[0] != '-')
                {
                    $prefix = is_numeric($params[0][0]) ? 'n' : null;
                    $prefix = $params[0][0] == '_' ? 'u' : $prefix;
                    $prefix = $params[0][0] == '-' ? 'd' : $prefix;

                    $action = array_shift($params);

                    $_r['action']['raw']        = $action;
                    $_r['action']['translated'] = $prefix . str_replace('-', '_', $action) . 'Action';

                } else {

                    array_shift($params);

                    $_r['action']['raw']        = 'index';
                    $_r['action']['translated'] = 'indexAction';
                }

            } else {

                $_r['action'] = ['raw' => 'index', 'translated' => 'indexAction'];
            }

            $_r['params'] = $params;

            return $_r;
        }
    }
