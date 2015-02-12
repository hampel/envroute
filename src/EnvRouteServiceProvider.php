<?php namespace EnvRoute;

use Illuminate\Filesystem\Filesystem;
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
		$this->publishes([
			__DIR__ . '/config/envroute.php' => config_path('envroute.php'),
		]);

		$this->mergeConfigFrom(
			__DIR__ . '/config/envroute.php', 'envroute'
		);

		$env = $this->app->environment();
		$packages = $this->app->config->get('envroute.packages');

		if (array_key_exists($env, $packages))
		{
			$package = $packages[$env];

			$files = new Filesystem();
			$files->requireOnce($package['path'] . '/vendor/autoload.php');

			if (array_key_exists('providers', $package))
			{
				foreach ($package['providers'] as $provider)
				{
					$this->app->register($provider);
				}
			}
		}
	}
}

?>
