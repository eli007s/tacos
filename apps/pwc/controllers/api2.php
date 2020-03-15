<?php

    class Api2_Controller
    {
        private $_tacos = [];

        public function __construct()
        {
            $file = $_SERVER['DOCUMENT_ROOT'] . '/db.json';

            if (file_exists($file))
            {
                $contents = file_get_contents($file);
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

                    //

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

        private function _listTacos($taco = '', $where = 'name')
        {
            return $this->_tacos;
        }
    }
