EnvRoute for Laravel
====================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hampel/envroute.svg?style=flat-square)](https://packagist.org/packages/hampel/envroute)
[![Total Downloads](https://img.shields.io/packagist/dt/hampel/envroute.svg?style=flat-square)](https://packagist.org/packages/hampel/alerts)
[![Open Issues](https://img.shields.io/bitbucket/issues/hampel/envroute.svg?style=flat-square)](https://bitbucket.org/hampel/envroute/issues)
[![License](https://img.shields.io/packagist/l/hampel/envroute.svg?style=flat-square)](https://packagist.org/packages/hampel/envroute)

By [Simon Hampel](mailto:simon@hampelgroup.com).

This package provides route-based environment detection and configuration for Laravel v5.x|v6.x|v7.x|v8.x|v9.x|v10.x
- it is intended for use in development environments for testing packages using Laravel as a test harness.

This is not just for developing Laravel specific packages - any type of package can use this environment for testing,
Laravel just provides a convenient test harness for us, making it easier to exercise our packages while developing.

About
-----

This package works on the assumption that you have multiple independent packages (that may or may not depend on Laravel)
which you develop locally and you have a single Laravel framework installation which you use as a test harness for
exercising your packages above and beyond what you might do in unit tests.

It does not matter where your packages are installed - I have started placing mine in the `/packages` directory under
the Laravel root folder, but the EnvRoute configuration for each package can be set to handle any location.
 
The other assumption is that you want to be able to autoload a specific package to work on, without autoloading other 
unrelated packages you might be working on.

This was one limitation of the Laravel 4.x Workbench in that it always autoloaded every package in the Workbench, which
sometimes lead to inconsistent or undesirable behaviour - particularly when you have packages depending on different
versions of the same package.

For example, let's say you develop PackageA, which has a dependency on PackageX v1.1.\*, and you also develop PackageB, 
which also has a dependency on PackageX, but requires v2.0.\*

In Workbench, you would run `composer update` to generate the autoload file for any dependencies on each package.
However, because all vendor autoload files from all packages are autoloaded, what happens is that PackageX v1.1.\* is 
autoloaded first (because this is the first one it finds when searching), and then PackageX v2.0.\* is never made 
available to your packageB when testing. The only way around this is to delete the `vendor` folder from PackageA before
testing PackageB, which is a pain - especially if you forget and subtle package dependency differences give you 
unexpected results which take you time to diagnose.

EnvRoute solves this by allowing you to selectively autoload only the dependencies you need and then set a custom
environment configuration to test in.

Installation
------------

The recommended way of installing the EnvRoute package is through [Composer](http://getcomposer.org):

	:::bash
	composer require "hampel/envroute"

Or manually define it in your `composer.json`

    :::json
    {
        "require": {
            "hampel/envroute": "^1.0"
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
    //	Illuminate\Contracts\Http\Kernel::class,
    //	App\Http\Kernel::class
    //);
    
    $app->singleton(
    	Illuminate\Contracts\Http\Kernel::class,
    	EnvRoute\Http\Kernel::class
    );

The EnvRoute version of the Http Kernel extends the default Http Kernel, so any future updates to the base framework
should not cause any problems.

**Note** - DO NOT change the namespace for your Laravel Framework - this package assumes that the base classes will be
found in the default `App` namespace.

Next, publish the EnvRoute configuration:

    :::bash
    $ php artisan vendor:publish --provider="EnvRoute\EnvRouteServiceProvider"

Follow the instructions in the usage section for how to configure EnvRoute to work with your packages. 

Usage
-----

### Routes ###

The EnvRoute package changes the default environment detection mechanism.

The intention is to create a route prefix for each package you are developing and then you can configure a unique
environment just for that package. For example - you could create a unique database connection for each package.
 
Start by creating a route group for your package in the `routes/web.php` file:

    :::php
    Route::prefix('test')->group(function() {
	
    	Route::get('/', function () {
    		// exercise your package at /test
    	});
    
    	Route::get('foo', function () {
    		// exercise your package at /test/foo
    	});
    });

Next, for our `test` route prefix, create a `.env` file suffixed by the name of the route prefix - in this example, we
would call our .env file `.env.test`. You can then configure this .env file with any package unique settings which will
be loaded whenever you visit a URL starting with `/test`.

**Important** - you must also set the APP_ENV variable within your .env file to match the route prefix.

	APP_NAME="My Test Package"
	APP_ENV=test
	
This is important, since it is the APP_ENV variable which will determine which packages are autoloaded by the EnvRoute
service provider.

### Autoloading ###

To set up autoloading of your package files, edit the `config/envroute.php`, configuration file to add the path to your
package. The list of packages is an array, with each key corresponding to the environment route we set up in the 
previous step.

    :::php
    'packages' => [
    
    	'test' => [
    		'path' => base_path() . '/packages/test',
    	],
    
    ],
    
In the above example, we have a package named 'test' (this doesn't have to correspond to the actual package name we are
testing - but it does have to correspond to the name of the route prefix). We define our route prefix for testing as
`/test` and a corresponding environment file `.env.test` and an APP_ENV value of `test`.
 
Our configuration tells the autoloader that the 'test' package is being developed in the folder `/packages/test` and 
once we have run `composer update` in this folder to load all package dependencies and create the autoload file, our 
package is ready for testing.

### Service Providers ###

If your package has Laravel service providers which need to be loaded, add them to a `providers` array key in the 
configuration. For example:

    :::php
    'packages' => [
    
    	'test' => [
    		'path' => base_path() . '/packages/test',
    
    		'providers' => [
    			'Test\TestServiceProvider',
    		],
    	],
    ],

Note that this isn't restricted to just your packages own service providers - any other service providers your package
(or test harness) depends on which aren't already loaded by the base framework should be added to the providers array. 

### Aliases ###

If your package has an alias (facade) which need to be loaded, add them to a `aliases` array key in the configuration.
For example:

    :::php
    'packages' => [
    
    	'test' => [
    		'path' => base_path() . '/packages/test',
    
    		'providers' => [
    			'Test\TestServiceProvider',
    		],
    		
    		'aliases' => [
    			'Test' => 'Test\Facades\Test',
    		]
    	],
    ],

