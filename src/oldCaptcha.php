<?php namespace Johntaa\Captcha;

use Config,  Session, Hash, URL, Response;

/**
 *
 * Laravel 4 Captcha package
 * @copyright Copyright (c) 2013 MeWebStudio
 * @version 1.0.0
 * @author Muharrem ER?N
 * @contact me@mewebstudio.com
 * @link http://www.mewebstudio.com
 * @date 2013-03-27
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */

class Captcha {
 
    /**
     * @var  Captcha config instance of the Captcha::$config object
     */
    public  $config = array();

    private  $id;
    private  $assets;
    private  $fonts = array();
    private  $backgrounds = array();
    private  $char;

    public  function __construct()
    {
  

    		$this->config = Config::get('captcha');
    		$this->assets = __DIR__ . '/../assets/';
    		$this->fonts = self::assets('fonts');
    		$this->backgrounds = self::assets('backgrounds');
			
			

    	//	return new Captcha();

    	 
    }

    /**
     * Generates a captcha image, writing it to the output
     * It is used internally by this bundle when pointing to "/captcha" (see [vendor]\routes.php)
     * Typically, you won't use this function, but use the above img() function instead
     * @param null $id
     * @access	public
     * @return mixed
    */
    public static function create($id = null)
    { 
		//if(empty(static::$config)) static::instance();
	
        static::$char = static::genRand(static::$config['length']);

        Session::put('captchaHash', Hash::make(static::$config['sensitive'] === true ? static::$char : strtolower(static::$char)));

    	static::$id = $id ? $id : static::$config['id'];

        $bg_image = static::asset('backgrounds');

        $bg_image_info = getimagesize($bg_image);
        if ($bg_image_info['mime'] == 'image/jpg' || $bg_image_info['mime'] == 'image/jpeg')
        {
            $old_image = imagecreatefromjpeg($bg_image);
        }
        elseif ($bg_image_info['mime'] == 'image/gif')
        {
            $old_image = imagecreatefromgif($bg_image);
        }
        elseif ($bg_image_info['mime'] == 'image/png')
        {
            $old_image = imagecreatefrompng($bg_image);
        }

        $new_image = imagecreatetruecolor(static::$config['width'], static::$config['height']);
        $bg = imagecolorallocate($new_image, 255, 255, 255);
        imagefill($new_image, 0, 0, $bg);

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, static::$config['width'], static::$config['height'], $bg_image_info[0], $bg_image_info[1]);

        $bg = imagecolorallocate($new_image, 255, 255, 255);
        for ($i = 0; $i < strlen(static::$char); $i++)
        {
            $color_cols = explode(',', static::asset('colors'));
            $fg = imagecolorallocate($new_image, trim($color_cols[0]), trim($color_cols[1]), trim($color_cols[2]));
            imagettftext($new_image, static::asset('fontsizes'), rand(-10, 15), 10 + ($i * static::$config['space']), rand(static::$config['height'] - 10, static::$config['height'] - 5), $fg, static::asset('fonts'), static::$char[$i]);
        }
        imagealphablending($new_image, false);

        $response = Response::make();
        $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Content-type', 'image/jpeg');
        $response->header('Content-Disposition', 'inline; filename=' . static::$id . '.jpg');

        imagejpeg($new_image, null, 80);
        imagedestroy($new_image);

        return $response;
    }

    /**
     * Fonts
     *
     * @access  public
     * @param   string
     * @return  array
     */
    public static function assets($type = null) {

    	$files = array();

    	if ($type == 'fonts')
    	{
    		$ext = 'ttf';
    	}
    	elseif ($type == 'backgrounds')
    	{
    		$ext = 'png';
    	}

    	if ($type)
    	{
			foreach (glob($this->assets . $type . '/*.' . $ext) as $filename)
			{
			    $files[] = $filename;
			}
		}

		return $files;

    }

    /**
     * Select asset
     *
     * @access  public
     * @param   string
     * @return  string
     */
    public static function asset($type = null)
    {

    	$file = null;

    	if ($type == 'fonts')
    	{
    		$file = static::$fonts[rand(0, count(static::$fonts) - 1)];
    	}
    	if ($type == 'backgrounds')
    	{
    		$file = static::$backgrounds[rand(0, count(static::$backgrounds) - 1)];
    	}
    	if ($type == 'fontsizes')
    	{
    		$file = static::$config['fontsizes'][rand(0, count(static::$config['fontsizes']) - 1)];
    	}
    	if ($type == 'colors')
    	{
    		$file = static::$config['colors'][rand(0, count(static::$config['colors']) - 1)];
    	}
        return $file;

    }

    /**
     * Checks if the supplied captcha test value matches the stored one
     *
     * @param	string	$value
     * @access	public
     * @return	bool
     */
    public static function check($value)
    {

		$captchaHash = Session::get('captchaHash');

        return $value != null && $captchaHash != null && Hash::check(static::$config['sensitive'] === true ? $value : strtolower($value), $captchaHash);

    }

    /**
     * Returns an URL to the captcha image
     * For example, you can use in your view something like
     * <img src="<?php echo Captcha::img(); ?>" alt="" />
     *
     * @access	public
     * @return	string
     */
    public static function img() {

		return URL::to('captcha?' . mt_rand(100000, 999999));

    }
	
	
	
	
	public static function genRand($len){

	//	$chars = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789';
		$chars = "2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ";
		$chrLen = strlen($chars);
		$chrLen--;

		$final=NULL;
		for($x=1;$x<=$len;$x++){
			$position = rand(0,$chrLen);
			$final .= substr($chars,$position,1);
		}

		return $final;
	} 
	
	
	
	
	
	
	
	
	

}


?>