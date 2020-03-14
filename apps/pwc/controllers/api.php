<?php

    class Api_Controller
    {
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
            $this->_db = new PDO('sqlite:tacos.sqlite3');

            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        public function indexAction()
        {
            echo '🌮';
        }

        public function tacosAction()
        {
            $this->_tacos();
        }

        private function _seed()
        {
            $statement = $this->_db->prepare('CREATE TABLE tacos (
                `id` INTEGER PRIMARY KEY,
                `name` VARCHAR,
                `tortilla` VARCHAR,
                `toppings` TEXT,
                `vegetarian` BOOLEAN,
                `soft` BOOLEAN
            )');
            
            $statement->execute();
            
            echo '<pre>', print_r($statement->errorInfo(), true);

            /*$insert = 'INSERT INTO tacos (name, tortilla, toppings, vegetarian, soft) VALUES (:name, :tortilla, :toppings, :vegetarian, :soft)';

            $statement = $this->_db->prepare($insert);

            foreach ($this->_schema as $k => $v)
            {
                $statement->bindParam(':' . $k, $v);
            }

            foreach ($this->_initialData as $k)
            {
                foreach ($k as $_k => $_v)
                {
                    if (in_array($k, $this->_schema))
                    {
                        $val = $_v;

                        if (is_bool($val))
                        {
                            $val = boolval($val);
                        }

                        $statement->bindValue(':' . $_k, $val);
                    }
                }

                $statement->execute();
            }*/

			echo '<pre>', print_r($statement->errorInfo(), true);

            return $this->_initialData;
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
