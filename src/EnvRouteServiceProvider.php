<?php namespace EnvRoute;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class EnvRouteServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->defineConfiguration();

		$env = $this->app->environment();
		$packages = $this->app->config->get('envroute.packages');

		if (array_key_exists($env, $packages))
		{
			$package = $packages[$env];

			$this->loadDependencies($package, $env);
			$this->registerServiceProviders($package);
			$this->registerAliases($package);
		}
	}

	protected function defineConfiguration()
	{
		$this->publishes([
			__DIR__ . '/config/envroute.php' => config_path('envroute.php'),
		], 'config');

		$this->mergeConfigFrom(
			__DIR__ . '/config/envroute.php', 'envroute'
		);
	}

	/**
	 * @param $package
	 * @param $env
	 */
	protected function loadDependencies($package, $env)
	{
		$files = new Filesystem();

		if (!isset($package['path']))
		{
			die("No path specified for package {$env} in envroute config");
		}

		$this->requirePackage($files, $package['path'], $env);

		if (isset($package['require']))
		{
			foreach ($package['require'] as $path)
			{
				$this->requirePackage($files, $path, $env);
			}
		}
	}

	protected function requirePackage(Filesystem $files, $path, $env)
	{
		if (!$files->exists($path . '/composer.json'))
		{
			die("Could not locate composer.json file, [" . $path . "] does not seem to be a valid package directory");
		}

		if (!$files->exists($path . '/vendor/autoload.php'))
		{
			die("Could not autoload dependencies for package {$env}, vendor autoload file not found. Please run 'composer update' in the folder [" . $path . "]");
		}

		// autoload dependencies
		$files->requireOnce($path . '/vendor/autoload.php');
	}

	/**
	 * @param $package
	 */
	protected function registerServiceProviders($package)
	{
		// register service providers
		if (array_key_exists('providers', $package))
		{
			foreach ($package['providers'] as $provider)
			{
				$this->app->register($provider);
			}
		}
	}

	/**
	 * @param $package
	 */
	protected function registerAliases($package)
	{
		// register aliases
		if (array_key_exists('aliases', $package))
		{
			$loader = AliasLoader::getInstance();

			foreach ($package['aliases'] as $key => $alias)
			{
				$loader->alias($key, $alias);
			}
		}
	}
}
