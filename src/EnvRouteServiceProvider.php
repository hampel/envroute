<?php namespace EnvRoute;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class EnvRouteServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

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
		if (!$files->exists($package['path'] . '/composer.json'))
		{
			die("Could not locate composer.json file, [" . $package['path'] . "] does not seem to be a valid package directory");
		}

		if (!$files->exists($package['path'] . '/vendor/autoload.php'))
		{
			die("Could not autoload dependencies for package {$env}, vendor autoload file not found. Please run 'composer update' in the folder [" . $package['path'] . "]");
		}

		// autoload dependencies
		$files->requireOnce($package['path'] . '/vendor/autoload.php');
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
