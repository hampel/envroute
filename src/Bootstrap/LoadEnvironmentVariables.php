<?php namespace EnvRoute\Bootstrap;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables as BaseLoadEnvironmentVariables;

class LoadEnvironmentVariables extends BaseLoadEnvironmentVariables {

	protected function checkForSpecificEnvironmentFile($app)
	{
        if ($app->runningInConsole() && ($input = new ArgvInput)->hasParameterOption('--env')) {
            if ($this->setEnvironmentFilePath(
                $app, $app->environmentFile().'.'.$input->getParameterOption('--env')
            )) {
                return;
            }
        }

        // EnvRoute uses the first segment of the path to determine the environment to load
		if (isset($_SERVER['REQUEST_URI']))
		{
			$uri = trim($_SERVER['REQUEST_URI'], '/');
			if (!empty($uri))
			{
				$paths = explode('/', $uri);
				if ($this->setEnvironmentFilePath($app, $app->environmentFile() . '.' . $paths[0]))
				{
					return;
				}
			}
		}

        if (! env('APP_ENV')) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app, $app->environmentFile().'.'.env('APP_ENV')
        );
	}

}
