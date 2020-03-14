<?php

    class Api_Controller
    {
        public function indexAction()
        {
            echo '🌮';
        }

        public function tacosAction()
        {
            $this->_taco('', 'get');

            echo '<pre>', print_r($_SERVER, true), '</pre>';
        }

        private function _taco($taco = '', $method = 'get')
        {
            switch ($method)
            {
                case 'put':

                    echo 'update 🌮';

                break;

                case 'delete':

                    echo 'delete 🌮';

                break;

                case 'post':

                    echo 'add 🌮s';

                break;

                default:

                    echo '🌮s';
            }
        }
    }
