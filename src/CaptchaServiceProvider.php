<?php

namespace Johntaa\Captcha;

use Illuminate\Support\ServiceProvider;

/**
 * Class CaptchaServiceProvider
 * @package Mews\Captcha
 */
class CaptchaServiceProvider extends ServiceProvider {

    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {
        // Publish configuration files
        $this->publishes([
            __DIR__.'/../config/captcha.php' => config_path('captcha.php')
        ], 'config');

        // HTTP routing
        $this->app['router']->get('captcha', '\Johntaa\Captcha\CaptchaController@getCaptcha');

        // Validator extensions
        $this->app['validator']->extend('captcha', function($attribute, $value, $parameters)
        {
			return  $this->app['captcha']->check($value); 
             
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    { 
        // Merge configs
        $this->mergeConfigFrom(
             __DIR__.'/../config/captcha.php', 'captcha'
        );

        // Bind captcha
      /*   $this->app->bind('captcha', function($app)
        {
         	return Captcha::instance(); 
        }); */
		$this->app->singleton('captcha', function($app)
		{
			return  new Captcha;
		});
	 
    }

}
