CHANGELOG
=========

1.1.0 (2020-06-17)
------------------

* tested on Laravel 7.x

1.0.2 (2019-10-08)
------------------

* tested on Laravel 6.x

1.0.1 (2019-03-27)
------------------

* tested on Laravel 5.5 - 5.8

1.0.0 (2019-03-27)
------------------

* rewrote EnvRoute\Bootstrap\LoadEnvironmentVariables to be a subclass of 
  Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables and simplify operation by augmenting the functionality of 
  function checkForSpecificEnvironmentFile to identify our route based env
* general code cleanup
* tied this version of Laravel 5.5 in composer.json

0.4.0 (2018-12-31)
------------------

* should now be compatible with Laravel v5.4+

0.3.0 (2015-02-13)
------------------

* added support for loading aliases

0.2.0 (2015-02-13)
------------------

* added service provider and configuration for defining service providers to be loaded for a specific environment

0.1.0 (2015-02-13)
------------------

* first version - environment detection and loading
