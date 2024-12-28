<?php

define('APP_ROOT',  __DIR__);

$logDirectory = APP_ROOT . '/logs';
$errorLogPath = $logDirectory . '/error.log';

if(!is_dir($logDirectory)){
    mkdir($logDirectory, 0777, true);
}

if(!file_exists($errorLogPath)){
    touch($errorLogPath);
}

function errorHandler($severity, $message, $file, $line) {
    global $errorLogPath;
    $error = date('[Y-m-d H:i:s]') . " [$severity] $message in $file on line $line" . PHP_EOL;
    error_log($error, 3, $errorLogPath);
}

set_error_handler('errorHandler');

function exceptionHandler($exception) {
    global $errorLogPath;
    $error = date('[Y-m-d H:i:s]') . ' [Exception] ' . $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine() . PHP_EOL;
    error_log($error, 3, $errorLogPath);
}

set_exception_handler('exceptionHandler');
