<?php

$path = __DIR__.'/../vendor/autoload.php';
include $path;

\PMVC\Load::plug(['unit' => null, 'controller'=>null], [__DIR__.'/../../']);

\PMVC\l(__DIR__.'/resources/FakeView');
\PMVC\l(__DIR__.'/resources/FakePlugIn');
\PMVC\l(__DIR__.'/resources/FakeAction');
\PMVC\l(__DIR__.'/resources/AnotherPlugin');
