<?php

    class Clean_Controller
    {
        public function indexAction()
        {
            $json = '';

            if (isset($_POST['text']) && strlen($_POST['text']) > 0)
            {
                $api = new Api_Controller();

                $api->text = $_POST['text'];

                $json = $api->cleanAction();
            }

            include_once (__DIR__ . '/../views/index.php');
        }
    }
