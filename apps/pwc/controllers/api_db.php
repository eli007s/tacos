<?php

    class Api_DB_Controller
    {
        private $_table = 'tacos';
        private $_database = 'tacos';

        private $_db = null;
        private $_schema = [
            'name',
            'tortilla',
            'toppings',
            'vegetarian',
            'soft'
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
                $this->_db->exec('DROP TABLE IF EXISTS `' . $this->_table . '`');
                $this->_db->exec('CREATE TABLE IF NOT EXISTS `' . $this->_table . '` (
                    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    `name` VARCHAR(160) NOT NULL,
                    `tortilla` VARCHAR(160) NOT NULL,
                    `toppings` TEXT NOT NULL,
                    `vegetarian` VARCHAR(5) NOT NULL,
                    `soft` VARCHAR(5) NOT NULL
                )');

                $insert = 'INSERT INTO tacos (`name`, `tortilla`, `toppings`, `vegetarian`, `soft`) VALUES (:name, :tortilla, :toppings, :vegetarian, :soft)';

                foreach ($this->_initialData as $k => $v)
                {
                    $statement[$k] = $this->_db->prepare($insert);

                    foreach ($v as $_j => $_i)
                    {
                        if (in_array($_j, $this->_schema))
                        {
                            $val = $_i;

                            if (is_bool($_i))
                            {
                                $val = ((int)$_i === 1 ? 'true' : 'false');

                            } else {

                                $val = (string)$_i;
                            }

                            $statement[$k]->bindValue(':' . $_j, $val, PDO::PARAM_STR);
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

                    $data['_taco'] = utf8_decode(urldecode(strtok($taco, '?')));

                    $taco = $this->_updateTaco($data);

                    echo json_encode($taco);

                break;

                case 'delete':

                    parse_str(file_get_contents("php://input"), $data);

                    $data['_taco'] = utf8_decode(urldecode(strtok($taco, '?')));

                    $taco = $this->_deleteTaco($data);

                    echo json_encode($taco);

                break;

                default:

                    $taco = utf8_decode(urldecode($taco));
                    $tacos = $this->_listTacos($taco);

                    echo json_encode($tacos);
                }
        }

        private function _listTacos($taco = '', $where = 'name')
        {
            $results = [];

            try
            {
                $query = 'SELECT * FROM `' . $this->_table . '`';

                if ($taco != '' && $where != '')
                {
                    $query .= ' WHERE `' . $where . '` = :' . $where;
                }

                $statement = $this->_db->prepare($query);

                if ($taco != '' && $where != '')
                {
                    $statement->bindValue(':' . $where, $taco, $where == 'id' ? PDO::PARAM_INT : PDO::PARAM_STR);
                }

                if ($statement->execute())
                {
                    $results = $statement->fetchAll();
                }

            } catch (PDOException $e) {

                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

                exit;
            }

            return ['status' => 'success', 'tacos' => $results];
        }

        private function _updateTaco($data)
        {
            $results = [];

            try
            {
                $taco = $this->_listTacos($data['_taco'], 'name');

                if ($taco['status'] == 'success' && count($taco['tacos']) > 0)
                {
                    $query = 'UPDATE `' . $this->_table . '` SET ';

                    foreach ($data as $k => $v)
                    {
                        if (in_array($k, $this->_schema))
                        {
                            $query .= '`' . $k . '` = :' . $k . ',';
                        }
                    }

                    $query = rtrim($query, ',') . ' WHERE `id` = :id';

                    $statement = $this->_db->prepare($query);

                    foreach ($data as $k => $v)
                    {
                        if (in_array($k, $this->_schema))
                        {
                            $statement->bindValue(':' . $k, (string)$v, PDO::PARAM_STR);
                        }
                    }

                    $statement->bindValue(':id', $taco['tacos'][0]['id'], PDO::PARAM_INT);

                    $statement->execute();

                    $results = $this->_listTacos($taco['tacos'][0]['id'], 'id');

                } else {

                    echo json_encode(['status' => 'error', 'message' => 'Taco ' . $data['_taco'] . ' not found.']);
                }

            } catch (PDOException $e) {

                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

                exit;
            }

            return $results;
        }

        private function _deleteTaco($data)
        {
            $results = [];

            try
            {
                $taco = $this->_listTacos($data['_taco'], 'name');

                if ($taco['status'] == 'success' && count($taco['tacos']) > 0)
                {
                    $query = 'DELETE FROM `' . $this->_table . '` WHERE `id` = :id';

                    $statement = $this->_db->prepare($query);

                    $statement->bindValue(':id', $taco['tacos'][0]['id'], PDO::PARAM_INT);

                    $statement->execute();

                    $results = $this->_listTacos();

                } else {

                    echo json_encode(['status' => 'error', 'message' => 'Taco ' . $data['_taco'] . ' not found.']);
                }

            } catch (PDOException $e) {

                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);

                exit;
            }

            return $results;
        }
    }
