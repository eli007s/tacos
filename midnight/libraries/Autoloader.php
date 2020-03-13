<?php

    class MN_Autoloader extends Midnight
    {
        private static $_path     = null;
        private static $_registry = [];

        public static function register($path)
        {
            self::$_path = $path;
        }

        public static function autoload($class)
        {
            if (!in_array($class, self::$_registry))
            {
                $rawArray           = explode('_', $class);

                if ($rawArray[0] == 'MN')
                {
                    $pathToFile = __DIR__;
                    $fileNames  = [ucfirst($rawArray[1])];

                } else {

                    if (strpos($rawArray[0], '\\') !== false)
                    {
                        $ns = explode('\\', $rawArray[0]);

                        $rawArray[0] = end($ns);
                    }

                    if (in_array(strtolower(end($rawArray)), ['controller', 'helper', 'model', 'view'])) {

                        $pathToFile = self::$_path . DS . strtolower(end($rawArray)) . 's';
                    }

                    if ($rawArray[0] !== 'Midnight')
                    {
                        if (count($rawArray) >= 3) {

                            $endOfArray = $rawArray[count($rawArray) - 1];

                            unset($rawArray[count($rawArray) - 1]);

                            $rawArray[0] = implode('_', $rawArray);

                            $rawArray[1] = $endOfArray;
                        }

                        $fileNames  = [
                            $rawArray[0],
                            strtolower($rawArray[1][0]) . ucfirst($rawArray[0]),
                            $rawArray[0] . '_' . strtolower($rawArray[1])
                        ];

                    } else {

                        $fileNames = [];
                    }
                }

                if (is_dir($pathToFile) && !empty($fileNames))
                {
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathToFile)) as $dir)
                    {
                        if ($dir->isFile())
                        {
                            foreach ($fileNames as $fileName)
                            {
                                if (strtolower($fileName . '.php') == strtolower($dir->getBasename()))
                                {
                                    self::$_registry[] = $class;

                                    require_once $dir->getPathname();

                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
