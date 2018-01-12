<?php

$path = __DIR__.'/../vendor/autoload.php';
include $path;

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class PHPUnit_Framework_TestCase extends
        \PHPUnit\Framework\TestCase
    {
    }
    class PHPUnit_Framework_Error extends
        \PHPUnit\Framework\Error\Notice
    {
    }
}

\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../../']);
\PMVC\plug('controller');
\PMVC\l(__DIR__.'/resources/FakeView.php');
\PMVC\l(__DIR__.'/resources/FakePlugIn.php');
\PMVC\l(__DIR__.'/resources/FakeAction.php');
\PMVC\l(__DIR__.'/resources/AnotherPlugin.php');
