<?php

    class Api_Controller
    {
        public function indexAction()
        {
            $this->_taco('', 'get');
        }

        public function tacosAction()
        {
            echo 'list of 🌮s';
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
