<?php

    class MN_Config
    {
        private static $_config    = [];
        private static $_namespace = '\\';
        private static $_view      = [];

        public static function load($config)
        {
            if (is_dir($config) || is_file($config))
            {
                if (is_dir($config))
                {
                    $scan = MN_Directory::scan($config);

                    foreach ($scan as $k => $v)
                    {
                        $c[] = $v['path'];
                    }

                } else {

                    $c[] = $config;
                }

                foreach ($c as $k => $v)
                {
                    $contents = [];

                    if (strpos($v, '.json') !== false)
                    {
                        $contents = json_decode(self::_cleanCommentsFromJson($v), true);
                    }

                    if (strpos($v, '.php') !== false)
                    {
                        $contents = file_get_contents($v);
                    }

                    if (is_array($contents))
                    {
                        self::$_config = array_merge(self::$_config, $contents);
                    }
                }

            } else {

                $config = json_decode($config, true);

                if (is_array($config))
                {
                    self::$_config = array_merge(self::$_config, $config);
                }
            }

            return self::_translate(self::$_config);
        }

        public static function app($app)
        {
            $app    = strtolower($app);
            $return = [];
            $config = self::array_change_key_case_recursive(self::$_config['apps']);

            if (isset($config[$app]))
            {
                $return = $config[$app];
            }

            return $return;
        }

        public static function apps()
        {
            return isset(self::$_config['apps']) ? self::$_config['apps'] : [];
        }

        public static function setNamespace($ns)
        {
            self::$_namespace = '\\' . ltrim($ns, '\\');
        }

        public static function getNamespace()
        {
            return self::$_namespace == '\\' ? self::$_namespace : self::$_namespace . '\\';
        }

        public static function setView($view)
        {
            self::$_view = $view;
        }

        public static function getView()
        {
            return self::$_view;
        }

        public static function getSettings()
        {
            return isset(self::$_config['settings']) ? self::$_config['settings'] : [];
        }

        private static function _cleanCommentsFromJson($file)
        {
            return preg_replace('@(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|((?<!:)//.*)|[\t\r\n]@i', '', file_get_contents($file));
        }

        private static function _translate($config)
        {
            if (isset(self::$_config['apps']))
            {
                foreach (self::$_config['apps'] as $k => $v)
                {
                    if (isset($v['import']))
                    {
                        if (isset(self::$_config['settings']['setting'][$v['import']]))
                        {
                            self::$_config['apps'][$k];
                        }
                    }
                }
            }

            return $config;
        }

        private static function array_change_key_case_recursive($arr, $case = CASE_LOWER)
        {
            return array_map(function($item) use($case) {

                if(is_array($item))
                {
                    $item = self::array_change_key_case_recursive($item, $case);
                }

                return $item;

            }, array_change_key_case($arr, $case));
        }
    }
