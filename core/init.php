<?php
/**
 * Created by PhpStorm.
 * User: dnyandika
 * Date: 9/12/2016
 * Time: 8:55 PM
 */

//Require all the classes that you will need || SPL means Standard PHP Library
//This php file automatically loads all classes that will be required for program flow
spl_autoload_register(function ($classes) {
    try {
        require_once 'core/' . $classes . '.php';
    } catch (Exception $ex) {
        $this->functions->writeToLog('ERROR', __METHOD__ . $ex->getMessage() . "() | Error ");
    }
});