<?php

    class Clean_Controller
    {
        public function indexAction()
        {
            echo file_get_contents(__DIR__ . '/../../views/index.html');
        }
    }
