<?php namespace Johntaa\Captcha;

use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('johntaa/captcha');

		require __DIR__ . '/../../routes.php';
		require __DIR__ . '/../../validation.php';

		$app = $this->app;

	    $this->app->finish(function() use ($app)
	    {

	    });
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
		$this->app['captcha'] = $this->app->share(function($app)
		  {
			return Captcha::instance();;
		  });

//If You Want to be able to Call your class without declare Facade alias in your app.php
// add the following:

	/* 	$this->app->booting(function()
		{
		  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
		  $loader->alias('Captcha', 'Johntaa\Captcha\Facades\Captcha');
		}); */
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('captcha');
			}

}