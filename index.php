<?php

    require('sys' . DIRECTORY_SEPARATOR . 'boot.php');

    //Determine the environment. This is not used somewhere else yet.
    if($_SERVER['SERVER_NAME'] == 'localhost' || strstr($_SERVER['SERVER_NAME'], '192.168.'))
    {
        $env = App::ENV_DEVELOPMENT;
    }
    else
    {
        $env = App::ENV_PRODUCTION;
    }
    
    if($env !== App::ENV_PRODUCTION && isset($_GET['env']))
    {
        $env = $_GET['env'];
    }
    
    //define('BLACKLIST_FOLDERS', 'mod/some,mod/another');
    define('LEAN_ENVIRONMENT', $env);
    define('LEAN_REWRITE', true);
    
    try //Execute the request
    {
        require('sys' . DS . 'setup.php');

        //Execute the request
        $output = Html::minify(Request::instance()->prepare()->execute()) 
        .'<!-- ' . Debug::instance()->memoryPeak() . 'MB / ' 
        . Debug::instance()->parsetime() . 'ms / ' 
        . Debug::$query_counter . ' queries -->';
              
        $debug = '';
        if(Debug::enabled() && Config::get('site.debug_regular'))
        {
            $debug = Html::minify(Debug::in()->render());
        }
        echo $output . $debug;
    }
    catch(Exception $e)
    {
        if(Debug::enabled())
        {
            echo Debug::exception($e);
        }
        else
        {
            exit;//todo: 404 / log..
        }
    }
