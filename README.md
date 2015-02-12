EnvRoute for Laravel
====================

This package provides route-based environment detection for Laravel v5.x - it is intended for use in development
environments for testing packages using Laravel as a test harness.

By [Simon Hampel](http://hampelgroup.com/).

Installation
------------

The package is built to work with the Laravel 5 Framework.

The recommended way of installing the EnvRoute package is through [Composer](http://getcomposer.org):

Require the package via Composer in your `composer.json`

    :::json
    {
        "require": {
            "hampel/envroute": "dev-master@dev"
        }
    }

Run Composer to update the new requirement.

    :::bash
    $ composer update

You will need to swap out the default Http Kernel with the one provided by this package by editing the app bootstrap
file. Edit `bootstrap/app.php`, remove or comment out the singleton loading for `App\Http\Kernel` and replace it with
`EnvRoute\Http\Kernel`.

    :::php
    //$app->singleton(
    //	'Illuminate\Contracts\Http\Kernel',
    //	'App\Http\Kernel'
    //);
    
    $app->singleton(
    	'Illuminate\Contracts\Http\Kernel',
    	'EnvRoute\Http\Kernel'
    );

The EnvRoute version of the Http Kernel extends the default Http Kernel, so any future updates to the base framework
should not cause any problems.

**Note** - DO NOT change the namespace for your Laravel Framework - this package assumes that the base classes will be
found in the default `App` namespace.

Usage
-----

The EnvRoute package changes the default environment detection mechanism for Laravel 5, from on the base route called.

The intention is to create a route prefix for each package you are developing and then you can configure a unique
environment just for that package. For example - you could create a unique database connection for each package.
 
Start by creating a route group for your package in the `app/Http/routes.php` file:

    :::php
    Route::group(array('prefix' => 'foo'), function()
    {
    	Route::get('/', function () {
    		// exercise your package at /foo
    	});
    
    	Route::get('bar', function () {
    		// exercise your package at /foo/bar
    	});
    });

Next, for our `foo` route prefix, create a `.env` file suffixed by the name of the route prefix - in this example, we
would call our .env file `.env.foo`. You can then configure this .env file with any package unique settings which will
be loaded whenever you visit a URL starting with `/foo`.

**Note** the APP_ENV setting in the .env file is ignored when using this package.
