<?php

    class Api2_Controller
    {
        private $_tacos = [];
        private $_db = '';

        public function __construct()
        {
            $this->_db = $_SERVER['DOCUMENT_ROOT'] . '/db.json';

            if (file_exists($this->_db))
            {
                $contents = file_get_contents($this->_db);
                $contents = utf8_encode($contents);

                $this->_tacos = json_decode($contents, true);

                header('content-type: application/json');
            }
        }

        public function tacosAction($taco = '')
        {
            $this->_tacos($taco);
        }

        private function _tacos($taco = '')
        {
            switch (strtolower($_SERVER['REQUEST_METHOD']))
            {
                case 'put':

                    parse_str(file_get_contents("php://input"), $data);

                    $taco = utf8_decode(urldecode(strtok($taco, '?')));

                    $output = $this->_updateTaco($taco, $data);

                    echo json_encode($output);

                break;

                case 'delete':

                    //

                break;

                default:

                    $taco = utf8_decode(urldecode($taco));
                    $tacos = $this->_listTacos($taco);

                    echo json_encode($tacos);
                }
        }

        private function _listTacos($taco = '')
        {
            $return = $this->_tacos;

            if ($taco != '')
            {
                // let's assume that we couldn't fine a taco. If we do find one, then $return will update.
                $return = ['tacos' => []];

                for ($i = 0; $i < count($this->_tacos['tacos']); $i++)
                {
                    if ($this->_tacos['tacos'][$i]['name'] === $taco)
                    {
                        $return = ['tacos' => $this->_tacos['tacos'][$i]];

                        break;
                    }
                }
            }

            return $return;
        }

        public function _updateTaco($taco, $data)
        {
            $return = $this->_tacos;
            $index  = '';

            if ($taco != '')
            {
                // let's assume that we couldn't fine a taco. If we do find one, then $return will update.
                $return = ['tacos' => []];

                for ($i = 0; $i < count($this->_tacos['tacos']); $i++)
                {
                    if ($this->_tacos['tacos'][$i]['name'] === $taco)
                    {
                        $taco = $this->_tacos['tacos'][$i];

                        foreach ($data as $k => $v)
                        {
                            if (array_key_exists($k, $taco))
                            {
                                $taco[$k] = $v;
                            }
                        }
print_r($taco);
                        $this->_tacos[$i] = $taco;

                        $fp = fopen($this->_db, 'w');

                        fwrite($fp, json_encode($this->_tacos));
                        fclose($fp);

                        break;
                    }
                }
            }
        }
    }
