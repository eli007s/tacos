<?php

    class Api_Controller
    {
        private $_table = 'tacos';
        private $_database = 'tacos';

        private $_db = null;
        private $_schema = [
            'name' => PDO::PARAM_STR,
            'tortilla' => PDO::PARAM_STR,
            'toppings' => PDO::PARAM_STR,
            'vegetarian' => PDO::PARAM_BOOL,
            'soft' => PDO::PARAM_BOOL
        ];
        private $_initialData = [
            [
                'name' => 'chorizo taco',
                'tortilla' => 'corn',
                'toppings' => 'chorizo',
                'vegetarian' => false,
                'soft' => true
            ],[
                'name' => 'chicken taco',
                'tortilla' => 'flour',
                'toppings' => 'chicken',
                'vegetarian' => false,
                'soft' => true
            ], [
                'name' => 'al pastor taco',
                'tortilla' => 'corn',
                'toppings' => 'pork',
                'vegetarian' => false,
                'soft' => true
            ],[
                'name' => 'veggie taco',
                'tortilla' => 'spinach',
                'toppings' => 'veggies',
                'vegetarian' => true,
                'soft' => true
            ]
        ];

        public function __construct()
        {
            $this->_db = new PDO('sqlite:' . __DIR__ . '/' . $this->_database . '.db');

            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function indexAction()
        {
            echo '🌮';
        }

        public function tacosAction()
        {
            //$this->_tacos();
        }

        public function seedAction()
        {
            try
            {
                $this->_db->exec('CREATE TABLE IF NOT EXISTS ' . $this->_table . ' (
                    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    name VARCHAR(160) NOT NULL,
                    tortilla VARCHAR(160) NOT NULL,
                    toppings TEXT NOT NULL,
                    vegetarian BOOLEAN NOT NULL,
                    soft BOOLEAN NOT NULL
                )');

                $insert = 'INSERT INTO tacos (name, tortilla, toppings, vegetarian, soft) VALUES (:name, :tortilla, :toppings, :vegetarian, :soft)';

                $statement = $this->_db->prepare($insert);

                foreach ($this->_schema as $k => $v)
                {
                    $statement->bindParam(':' . $k, $v);
                }

                foreach ($this->_initialData as $k)
                {//print_r($k);
                    foreach ($k as $_k => $_v)
                    {echo '______'.$_k.'__'.$_v;
                        if (in_array($_k, $this->_schema))
                        {
                            $val = $_v;

                            if ($k == 5)
                            {
                                $val = boolval($_v);

                            } else {

                                $val = (string)$_v;
                            }

                            //echo "\nbindValue::$_k = $val\n";
                            $statement->bindValue(':' . $_k, $val);
                        }
                    }

                    $statement->execute();
                }

                $statement = $this->_db->query('SELECT * FROM ' . $this->_table);
                $results = $statement->fetch(\PDO::FETCH_ASSOC);

                echo '<pre>', print_r($results, true), '</pre>';

            } catch (PDOException $e) {

                echo $e->getMessage();
            }

            //return $this->_initialData;
        }

        private function _tacos()
        {
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'put':

                    echo 'update 🌮';

                break;

                case 'delete':

                    echo 'delete 🌮';

                break;

                case 'post':

                    $this->_db->exec('BEGIN');

                    $this->_db->query('INSERT INTO "tacos" ("name", "tortilla", "toppings", "vegetarian", "soft")
                    VALUES (' . $_POST['name'] . ', ' . $_POST['tortilla'] . ', ' . $_POST['toppings'] . ', ' . $_POST['vegetarian'] . ',' . $_POST['soft'] . ')');

                    echo 'add 🌮s';

                break;

                case 'GET':

                    $tacos = $this->_listTacos();

                    echo '<pre>', print_r($tacos, true), '</pre>';

                    return '';

                default:

                    echo '🌮';
            }
        }

        private function _listTacos()
        {
            $results = null;

            try
            {
                $statement = $this->_db->prepare('SELECT * FROM tacos');

                $results = $statement->execute();

                if ($results)
                {
                    echo '<pre>', print_r($results, true), '</pre>';
                }

            } catch (PDOException $e) {

                $code = $e->getCode();

                if ($code === 'HY000')
                {
                    return $this->_seed();
                }
            }

            return $results;
        }
    }
