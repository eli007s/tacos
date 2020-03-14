<?php

    class Api_Controller
    {
        private $_db = null;
        private $_initialData = [
            "tacos" => [
                [
                    "name" => "chorizo taco",
                    "tortilla" => "corn",
                    "toppings" => "chorizo",
                    "vegetarian" => false,
                    "soft" => true
                ],[
                    "name" => "chicken taco",
                    "tortilla" => "flour",
                    "toppings" => "chicken",
                    "vegetarian" => false,
                    "soft" => true
                ], [
                    "name" => "al pastor taco",
                    "tortilla" => "corn",
                    "toppings" => "pork",
                    "vegetarian" => false,
                    "soft" => true
                ],[
                    "name" => "veggie taco",
                    "tortilla" => "spinach",
                    "toppings" => "veggies",
                    "vegetarian" => true,
                    "soft" => true
                ]
            ]
        ];

        public function indexAction()
        {
            echo '🌮';
        }

        public function tacosAction()
        {
            if (!file_exists('tacos.sqlite3'))
            {
                $this->_db = new PDO('sqlite:tacos.sqlite3');

                $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->_db->exec('CREATE TABLE IF NOT EXISTS "tacos" (
                    "id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                    "name" VARCHAR,
                    "tortilla" VARCHAR,
                    "toppings" TEXT,
                    "vegetarian" BOOLEAN,
                    "soft" BOOLEAN
                )');

                $insert = 'INSERT INTO tacos (name, tortilla, toppings, vegetarian, soft) VALUES (:name, :tortilla, :toppings, :vegetarian, :soft)';

                $statement = $this->_db->prepare($insert);

                $statement->bindParam(':name', PDO::PARAM_STR);
                $statement->bindParam(':tortilla',PDO::PARAM_STR);
                $statement->bindParam(':toppings', PDO::PARAM_STR);
                $statement->bindParam(':vegetarian', PDO::PARAM_BOOL);
                $statement->bindParam(':soft', PDO::PARAM_BOOL);

                foreach ($this->_initialData as $k => $v)
                {
                    $statement->bindValue(':name', $v['name']);
                    $statement->bindValue(':tortilla', $v['tortilla']);
                    $statement->bindValue(':toppings', $v['toppings']);
                    $statement->bindValue(':vegetarian', $v['vegetarian']);
                    $statement->bindValue(':soft', $v['soft']);

                    $statement->execute();
                }
            }

            $this->_taco('');
        }

        private function _taco($taco = '')
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

                default:

                    echo '🌮s';
            }
        }
    }
