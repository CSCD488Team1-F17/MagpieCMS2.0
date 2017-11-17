<?php
/*
config.php

This is a simple file that returns an array as an object.
This stores some of our custom settings that we use throughout our application, namely database information.
*/
    return (object) array(
        //Database related
        'server' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'magpie',
        
        /*
        The location and name of our Google API client/secret file provided by Google.
        This file is necessary for our PHP Google library.

        dirname(__FILE__, 1)    // This simply gets the path of wherever config.php is.
                                // If you wish to move the .json file somewhere else other
                                // than the same folder as config.php you will have to modify this line.
                                //
                                // Furthermore, the string must be the full absolute path, not relative.

        */
        'credentialsFile' => dirname(__FILE__, 1) . '/client_secret.json',
        
    );

?>