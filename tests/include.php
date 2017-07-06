<?php

namespace PMVC;

include __DIR__.'/../vendor/autoload.php';
\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../../']);
\PMVC\plug('controller');
l(__DIR__.'/resources/FakeView.php');
l(__DIR__.'/resources/FakePlugIn.php');
l(__DIR__.'/resources/FakeAction.php');
l(__DIR__.'/resources/AnotherPlugin.php');
