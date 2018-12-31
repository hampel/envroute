<?php namespace EnvRoute\Http;

use App\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Foundation\Application;

class Kernel extends HttpKernel {

	/**
	 * Create a new HTTP kernel instance.
	 *
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @param  \Illuminate\Routing\Router  $router
	 */
	public function __construct(Application $app, Router $router)
	{
		$key = array_search('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables', $this->bootstrappers());
		$this->bootstrappers[$key] = 'EnvRoute\Bootstrap\LoadEnvironmentVariables';

		parent::__construct($app, $router);
	}

}
