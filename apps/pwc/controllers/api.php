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
            $this->_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            header('content-type: application/json');
        }

        public function indexAction()
        {
            echo '🌮';
        }

        public function tacosAction($taco = '')
        {
            $this->_tacos($taco);
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

                foreach ($this->_initialData as $k => $v)
                {
                    $statement[$k] = $this->_db->prepare($insert);

                    foreach ($this->_schema as $_k => $_v)
                    {
                        $statement[$k]->bindParam(':' . $_k, $_v);
                    }

                    foreach ($v as $_j => $_i)
                    {
                        if (array_key_exists($_j, $this->_schema))
                        {
                            $val = $_i;

                            if ($this->_schema[$_j] === 5)
                            {
                                $val = ((int)$_i === 1 ? 'true' : 'false');

                                filter_var($val, FILTER_VALIDATE_BOOLEAN);

                            } else {

                                $val = (string)$_i;
                            }

                            $statement[$k]->bindValue(':' . $_j, $val);
                        }
                    }

                    $statement[$k]->execute();
                }

            } catch (PDOException $e) {

                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

                exit;
            }

            echo json_encode(['status' => 'success']);
        }

        private function _tacos($taco = '')
        {
            switch (strtolower($_SERVER['REQUEST_METHOD']))
            {
                case 'put':

                    parse_str(file_get_contents("php://input"), $data);

                    if (is_array($data))
                    {
                        $data = array_keys($data)[0];
                        echo '<pre>', print_r($data, true), '</pre>';
                    }

                    //echo '<pre>', print_r($data, true), '</pre>';

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

                default:

                    $taco = utf8_decode(urldecode($taco));
                    $tacos = $this->_listTacos($taco);

                    echo json_encode($tacos);
                }
        }

        private function _listTacos($taco = '')
        {
            $results = [];

            try
            {
                $query = 'SELECT * FROM ' . $this->_table;

                if ($taco != '')
                {
                    $query .= ' WHERE `name` = :name';
                }

                $statement = $this->_db->prepare($query);

                if ($taco != '')
                {
                    $statement->bindParam(':name', $taco);
                    $statement->bindValue(':name',  $taco);
                }

                if ($statement->execute())
                {
                    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                }

            } catch (PDOException $e) {

                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

                exit;
            }

            return ['tacos' => $results];
        }

        private function _updateTaco($data)
        {
            //
        }
    }
