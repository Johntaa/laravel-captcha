<?php namespace Johntaa\Captcha;

use Config,  Session, Hash, URL, Response;

/**
 *
 * Laravel 5 Captcha package 
 * @version 1.0.0
 * @Originally authored by Muharrem ER?N
 * @contact me@mewebstudio.com
 * @link http://www.mewebstudio.com
 * @date 2013-03-27
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * updated by johntaa 
 */

class Captcha {
 
    /**
     * @var  Captcha config instance of the Captcha::$config object
     */
    public  $config = [];

    private  $id;
    private  $assets;
    private  $fonts = [];
    private  $backgrounds = [];
    private  $char;

    public  function __construct()
    {
   
    		$this->config = Config::get('captcha');
    		$this->assets = __DIR__ . '/../assets/';
    		$this->fonts = $this->assets('fonts');
    		$this->backgrounds = $this->assets('backgrounds');
	   	 
    }

    /**
     * Generates a captcha image, writing it to the output
     * @param null $id
     * @access	public
     * @return mixed
    */
    public  function create($id = null)
    { 
		//if(empty($this->$config)) $this->instance();
	
        $this->char = $this->genRand($this->config['length']);

        Session::put('captchaHash', Hash::make($this->config['sensitive'] === true ? $this->char : strtolower($this->char)));

    	$this->id = $id ? $id : $this->config['id'];

        $bg_image = $this->asset('backgrounds');

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

        $new_image = imagecreatetruecolor($this->config['width'], $this->config['height']);
        $bg = imagecolorallocate($new_image, 255, 255, 255);
        imagefill($new_image, 0, 0, $bg);

        imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $this->config['width'], $this->config['height'], $bg_image_info[0], $bg_image_info[1]);

        $bg = imagecolorallocate($new_image, 255, 255, 255);
        for ($i = 0; $i < strlen($this->char); $i++)
        {
            $color_cols = explode(',', $this->asset('colors'));
            $fg = imagecolorallocate($new_image, trim($color_cols[0]), trim($color_cols[1]), trim($color_cols[2]));
            imagettftext($new_image, $this->asset('fontsizes'), rand(-10, 15), 10 + ($i * $this->config['space']), rand($this->config['height'] - 10, $this->config['height'] - 5), $fg, $this->asset('fonts'), $this->char[$i]);
        }
        imagealphablending($new_image, false);

        $response = Response::make();
        $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Content-type', 'image/jpeg');
        $response->header('Content-Disposition', 'inline; filename=' . $this->id . '.jpg');

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
    public  function assets($type = null) {

    	$files = [];

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
    public  function asset($type = null)
    {

    	$file = null;

    	if ($type == 'fonts')
    	{
    		$file = $this->fonts[rand(0, count($this->fonts) - 1)];
    	}
    	if ($type == 'backgrounds')
    	{
    		$file = $this->backgrounds[rand(0, count($this->backgrounds) - 1)];
    	}
    	if ($type == 'fontsizes')
    	{
    		$file = $this->config['fontsizes'][rand(0, count($this->config['fontsizes']) - 1)];
    	}
    	if ($type == 'colors')
    	{
    		$file = $this->config['colors'][rand(0, count($this->config['colors']) - 1)];
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
    public  function check($value)
    {

		$captchaHash = Session::get('captchaHash');

        return $value != null && $captchaHash != null && Hash::check($this->config['sensitive'] === true ? $value : strtolower($value), $captchaHash);

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
	
	
	/**
	 *  Return Random AlphaNumeric string
	 *  @access	public
	 *  @param [in] $len string length
	 */
	
	public  function genRand($len){
 
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