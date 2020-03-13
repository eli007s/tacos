<?php

    /**
     * Apply a global timezone, timezones are handled by the application settings
     */
    date_default_timezone_set('UTC');

    /**#@+
     * Constants
     */

    /**
     * Global Directory Separator
     */
    define('DS', DIRECTORY_SEPARATOR);

    /**
     * The path to where the init file should be
     * @var string
     */
    $controller = __DIR__ . DS . 'midnight' . DS . 'libraries' . DS . 'Midnight.php';

    if (is_file($controller))
    {
        if (session_id() == '')
        {
            session_start();
        }

        require_once($controller);

        $midnight = new Midnight();

    } else {

        $margin = 0;
        $style  = null;
        $font   = 'font-family: \'HelveticaNeue-Light\', \'Helvetica Neue Light\', \'Helvetica Neue\',';
        $font  .= '\'Helvetica, Arial\', \'Lucida Grande\', sans-serif;';

        echo <<<EOF
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <title>Midnight Framework</title>
    </head>
    <body>
        <div style="border: 1px solid #DBDBDB; border-bottom: 3px solid #DBDBDB; width: 600px; margin: 50px auto;">
            <div style="padding: 25px; {$font}">
                <span style="display: block;">
                    Oops, your installation seems iffy.
                    <br />
                    <br />
                    Please check that your installation paths look like this:
                </span>
                <div style="border-left: 2px solid #878787; padding: 5px 0 8px; margin: 30px 0;">
EOF;
        foreach (['midnight', 'libraries', 'Midnight.php'] as $path)
        {
            $margin += 20;
            $style  .= 'display: block; margin: 8px 0 0 ' . $margin . 'px; height: 15px;';
            $style  .= 'padding-left: 8px; border-left: 1px solid #000;';

            echo <<<EOF
                    <span style="{$style}">{$path}</span>
EOF;
        }

        echo <<<EOF
                </div>
            </div>
        </div>
    </body>
</html>
EOF;

        exit();
    }
