<?php

    class Api2_Controller
    {
        private $_tacos = [];

        public function __construct()
        {
            $this->_tacos = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/db.json');
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
            return ['status' => 'success', 'tacos' => $this->_tacos];
        }
    }
