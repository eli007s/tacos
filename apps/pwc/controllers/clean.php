<?php

    class Clean_Controller
    {
        public function indexAction()
        {
            if (isset($_POST['text']) && strlen($_POST['text']) > 0)
            {

            }

            echo file_get_contents(__DIR__ . '/../views/index.php');
        }
    }
