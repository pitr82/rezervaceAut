<?php
namespace App;

/**
 * Description of myHelpers
 *
 * @author petr
 */
class myHelpers {
    /**
     * Method we will register as callback
     * in method $template->registerHelperLoader().
     */
    public function loader($name)
    {
    $name = func_get_args();
    $func = $name[0];
    unset($name[0]);
    if (method_exists($this, $func))
    {
        return call_user_func_array(array($this, $func), $name);
    }
    else return null;
    }
    /* === Following particular helpers === */

    /**
     * Display profile photos
     *
     * <code>
     * <img src="{'JohnDoe.jpg'|profilePicture}">
     * </code>
     *
     * @param  string name of file with photo
     * @return string route to profile photo of default picture
     */
    public function orezText($s, $len = 10)
    {
        return mb_substr($s, 0, $len);
    }
    
    public function orezTextNa2($s) {
        return mb_substr($s, 0, 2); // zkrátí text na 10 písmen
    }
}
