<?php

    class Clean_Controller
    {
        public function indexAction()
        {
            if (isset($_POST['text']) && strlen($_POST['text']) > 0)
            {
                //
            }

            include_once (__DIR__ . '/../views/index.php');
        }
    }
