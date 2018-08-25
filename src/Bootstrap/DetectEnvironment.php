<?php namespace EnvRoute\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Contracts\Foundation\Application;

class DetectEnvironment {

	/**
	 * Bootstrap the given application.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return void
	 */
	public function bootstrap(Application $app)
	{
		$app->detectEnvironment(function() use ($app)
		{
			if (!isset($_SERVER['REQUEST_URI'])) return $this->loadEnvironment($app);

			$uri = trim($_SERVER['REQUEST_URI'], '/');
			if (empty($uri)) return $this->loadEnvironment($app);

			$paths = explode('/', $uri);

			return $this->loadEnvironment($app, $paths[0]);
		});
	}

	protected function loadEnvironment(Application $app, $path = "")
	{
		if (!empty($path))
		{
			$file = ".env.{$path}";

			if (file_exists($app->environmentPath().'/'.$file))
			{
				$app->loadEnvironmentFrom(".env.{$path}");
			}
		}

		try
		{
			(new Dotenv($app->environmentPath(), $app->environmentFile()))->load();
		}
		catch (InvalidPathException $e)
		{
			die($e->getMessage());
		}

		return $path;
	}

}
