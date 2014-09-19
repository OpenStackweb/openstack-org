<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 9/5/13
 * Time: 11:24 AM
 * To change this template use File | Settings | File Templates.
 */

class CustomTimeField extends TimeField{
    function __construct($name, $title = null, $value = ""){
        parent::__construct($name, $title, $value);
    }

    protected function FieldDriver($html) {
        if($this->getConfig('showdropdown')) {
            Requirements::javascript(SAPPHIRE_DIR . '/thirdparty/prototype/prototype.js');
            Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
            Requirements::javascript(SAPPHIRE_DIR . '/javascript/jquery_improvements.js');
            Requirements::javascript(THIRDPARTY_DIR . '/behaviour/behaviour.js');
            Requirements::javascript(SAPPHIRE_DIR . '/javascript/TimeField_dropdown.js');
            Requirements::css(SAPPHIRE_DIR . '/css/TimeField_dropdown.css');
            Requirements::css(THEMES_DIR . '/openstack/css/custom.timefield.css');

            $html .= sprintf('<img class="timeIcon" src="sapphire/images/clock-icon.gif" id="%s-icon"/>', $this->id());
            $html .= sprintf('<div class="dropdownpopup" id="%s-dropdowntime"></div>', $this->id());
            $html = '<div class="dropdowntime">' . $html . '</div>';
        }

        return $html;
    }
}