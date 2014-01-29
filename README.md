# Captcha for Laravel 4.1
It is just a copy of https://github.com/mewebstudio/captcha but maintained to serve Laravel 4.1

A simple [Laravel 4.1](http://laravel.com/) service provider for including the [Captcha for Laravel 4.1](https://github.com/johntaa/captcha).

## Preview
![Preview](http://i.imgur.com/kfXYhlk.jpg?1)



## Installation



The Captcha Service Provider can be installed via [Composer](http://getcomposer.org) by requiring the
`mews/captcha` package and setting the `minimum-stability` to `dev` (required for Laravel 4.1) in your
project's `composer.json`.

```json
{
    "require": {
        "laravel/framework": "4.*",
        "johntaa/captcha": "dev-master" 
    },
    "minimum-stability": "dev"
}
```

Update your packages with ```composer update``` or install with ```composer install```.

## Usage

To use the Captcha Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `app/config/app.php` and register the Captcha Service Provider.

```php
    'providers' => array(
        // ...
        'Johntaa\Captcha\CaptchaServiceProvider',
    )
```

Find the `aliases` key in `app/config/app.php`.

```php
    'aliases' => array(
        // ...
        'Captcha' => 'Johntaa\Captcha\Facades\Captcha',
    )
```

## Configuration

To use your own settings, publish config.

```$ php artisan config:publish johntaa/captcha```

## Example Usage

```php

    // [your site path]/app/routes.php

    Route::any('/captcha-test', function()
    {

        if (Request::getMethod() == 'POST')
        {
            $rules =  array('captcha' => array('required', 'captcha'));
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails())
            {
                echo '<p style="color: #ff0000;">Incorrect!</p>';
            }
            else
            {
                echo '<p style="color: #00ff30;">Matched :)</p>';
            }
        }

        $content = Form::open(array(URL::to(Request::segment(1))));
        $content .= '<p>' . HTML::image(Captcha::img(), 'Captcha image') . '</p>';
        $content .= '<p>' . Form::text('captcha') . '</p>';
        $content .= '<p>' . Form::submit('Check') . '</p>';
        $content .= '<p>' . Form::close() . '</p>';
        return $content;

    });
```
 
