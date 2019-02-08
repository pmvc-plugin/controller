[![Latest Stable Version](https://poser.pugx.org/pmvc-plugin/controller/v/stable)](https://packagist.org/packages/pmvc-plugin/controller) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc-plugin/controller/v/unstable)](https://packagist.org/packages/pmvc-plugin/controller) 
[![Build Status](https://travis-ci.org/pmvc-plugin/controller.svg?branch=master)](https://travis-ci.org/pmvc-plugin/controller)
[![StyleCI](https://styleci.io/repos/56382568/shield)](https://styleci.io/repos/56382568)
[![Coverage Status](https://coveralls.io/repos/github/pmvc-plugin/controller/badge.svg?branch=master)](https://coveralls.io/github/pmvc-plugin/controller?branch=master)
[![License](https://poser.pugx.org/pmvc-plugin/controller/license)](https://packagist.org/packages/pmvc-plugin/controller)
[![Total Downloads](https://poser.pugx.org/pmvc-plugin/controller/downloads)](https://packagist.org/packages/pmvc-plugin/controller) 

PMVC Controller
===============
   * A simple MVC for unidirectional dataflow architecture.
   * <img src="https://raw.githubusercontent.com/pmvc/pmvc.github.io/master/flow5.png">
   * More information https://github.com/pmvc/pmvc

## Explain flow
controller -> plugapp -> process -> execute -> _processForm -> _processValidate -> _processAction -> processForward -> _finish

## Explain App Folder
```
- Site Folder (_RUN_APPS's parent folder, \PMVC\plug('controller')->getAppsParent())
-- Apps Folder (_RUN_APPS)
--- App Folder (_RUN_APP)
```

## APP customize view and template
   * View
      * view_engine_[app]=[html|json|react|...]
   * Template
      * template_dir_[app]=[forder path]

## Install with Composer
### 1. Download composer
   * mkdir test_folder
   * curl -sS https://getcomposer.org/installer | php

### 2. Install by composer.json or use command-line directly
#### 2.1 Install by composer.json
   * vim composer.json
```
{
    "require": {
        "pmvc-plugin/controller": "dev-master"
    }
}
```
   * php composer.phar install

#### 2.2 Or use composer command-line
   * php composer.phar require pmvc-plugin/controller

