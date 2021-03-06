<?php

    class Api_Controller
    {
        private $_tacos = [];
        private $_db = '';
        private $_text = '';

        public $text = '';

        public function __construct()
        {
            $this->_db = $_SERVER['DOCUMENT_ROOT'] . '/db.json';
            $this->_text = $_SERVER['DOCUMENT_ROOT'] . '/textSampleResults.json';

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

        public function cleanAction()
        {
            // grab text from the POST or load from file
            $text = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/textSample.txt');

            if (isset($_POST['text']) && strlen($_POST['text']) > 0)
            {
                $text = $_POST['text'];
            }

            $charsToFind = 'RSTLNAEIOU';

            preg_match_all("/(\w+(['-\w+])*)/i", $text, $matches);

            if (count($matches[0] > 0))
            {
                $clean = [];
                $object = [];

                // we have content to work with yay
                foreach ($matches[0] as $k => $word)
                {
                    $_word = preg_replace('/[RSTLNAEIOU]/i', '', $word);

                    if (strlen($_word) === 1)
                    {
                        $clean[] = $word;
                    }
                }

                // now that we have our filtered array, lets grab unique words and tally them up
                $clean = array_count_values($clean);

                arsort($clean);

                foreach ($clean as $k => $v)
                {
                    $object[] = [
                        'word' => $k,
                        'numberOfUses' => $v
                    ];
                }

                $fp = fopen($this->_text, 'w');

                fwrite($fp, json_encode($object));
                fclose($fp);

                echo json_encode($object);
            }
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

                    $taco = utf8_decode(urldecode(strtok($taco, '?')));

                    $output = $this->_deleteTaco($taco);

                    echo json_encode($output);

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
                            // let's check if the key being passed exists
                            if (array_key_exists($k, $taco))
                            {
                                if ($v === 'true' || $v == 'false')
                                {
                                    $v = filter_var($v, FILTER_VALIDATE_BOOLEAN);;
                                }

                                $taco[$k] = $v;
                            }
                        }

                        $this->_tacos['tacos'][$i] = $taco;

                        $fp = fopen($this->_db, 'w');

                        fwrite($fp, json_encode($this->_tacos));
                        fclose($fp);

                        $return = ['tacos' => $this->_tacos['tacos'][$i]];

                        break;
                    }
                }
            }

            return $return;
        }

        private function _deleteTaco($taco)
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
                        unset($this->_tacos['tacos'][$i]);

                        $fp = fopen($this->_db, 'w');

                        fwrite($fp, json_encode($this->_tacos));
                        fclose($fp);

                        $return = $this->_tacos;

                        break;
                    }
                }
            }

            return $return;
        }
    }
