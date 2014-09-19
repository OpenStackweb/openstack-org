<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 7/18/13
 * Time: 2:07 PM
 * To change this template use File | Settings | File Templates.
 */

class Upload_Image_Validator extends  Upload_Validator {

    private $allowedMaxImageWidth = null;

    /**
     * Sets Maximum allowed Width for image
     *
     * @param int $width
     */
    public function setAllowedMaxImageWidth(number $width){
        $this->allowedMaxImageWidth = $width;
    }
    /**
     * Determines if the pixels of an image uploaded
     * file is valid - can be defined on an
     * extension-by-extension basis in {@link $allowedMaxFileSize}
     *
     * @return boolean
     */
    public function isValidWidth() {
	    if(isset($this->tmpFile['tmp_name'])){
		    list($width, $height) = getimagesize($this->tmpFile['tmp_name']);
		    if(isset($this->allowedMaxImageWidth)){
			    return ((int) $width <= $this->allowedMaxImageWidth);
		    }
	    }
        return true;
    }

    public function validate() {
        $res = parent::validate();
        // width validation
        if(!$this->isValidWidth()) {
            $this->errors[] = sprintf("Max. Allowed Image Width is %d px",$this->allowedMaxImageWidth);
            $res = false;
        }
        return $res;
    }
}