<?php
namespace Johntaa\Captcha;

use Illuminate\Routing\Controller;
 
/**
 * Class CaptchaController
 * @package Johntaa\Captcha
 */
class CaptchaController extends Controller
{

    /**
     * get CAPTCHA
     *
     * @param \Johntaa\Captcha\Captcha $captcha
     * @param string $id 
     */
    public function getCaptcha($id=null)
    { 
			$captcha = new Captcha;
        return  $captcha->create($id);
    }

}
