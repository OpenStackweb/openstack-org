<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 9/13/13
 * Time: 12:29 PM
 * To change this template use File | Settings | File Templates.
 */

require_once 'Zend/Date.php';

class JQueryUIDatePickerField extends TextField {

    private $data_dependant;

    protected $locale = null;

    protected $valueObj = null;

    public function __construct($name, $title = null, $value = '', $form = null,$data_dependant=""){
        $this->data_dependant = $data_dependant;
        if(!$this->locale) {
            $this->locale = i18n::get_locale();
        }

        parent::__construct($name, $title, $value, 6, $form);
    }

    function Field() {
        $this->addExtraClass('DatePickerField');
        Requirements::block(SAPPHIRE_DIR .'/thirdparty/jquery/jquery.js');

        Requirements::css("themes/openstack/javascript/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.min.css");

        Requirements::javascript('themes/openstack/javascript/jquery-2.0.3.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery-migrate-1.2.1.min.js');
        Requirements::javascript("themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js");
        Requirements::javascript("datepicker/javascript/datepicker.js");
        
        $attributes = array(
            'type' => 'text',
            'class' => 'text' . ($this->extraClass() ? $this->extraClass() : ''),
            'id' => $this->id(),
            'name' => $this->Name(),
            'value' => $this->Value(),
            'tabindex' => $this->getTabIndex(),
            'maxlength' => ($this->maxLength) ? $this->maxLength : null,
            'size' => ($this->maxLength) ? min( $this->maxLength, 10 ) : null,
        );
        if(!empty($this->data_dependant)){
            $attributes["data-dependant-on"] = $this->data_dependant;
        }
        if($this->disabled) $attributes['disabled'] = 'disabled';
        return $this->createTag('input', $attributes);
    }

    /**
     * Sets the internal value to ISO date format.
     *
     * @param String|Array $val
     */
    function setValue($val) {
        if(empty($val)) {
            $this->value = null;
            $this->valueObj = null;
        } else {
            // Quick fix for overzealous Zend validation, its case sensitive on month names (see #5990)
            if(is_string($val)) $val = ucwords(strtolower($val));
                // load ISO date from database (usually through Form->loadDataForm())
               if(!empty($val) && Zend_Date::isDate($val, 'yyyy-MM-dd')) {
                    $this->valueObj = new Zend_Date($val, 'yyyy-MM-dd');
                    $this->value = $this->valueObj->get('yyyy-MM-dd', $this->locale);
                }
                else {
                    $this->value = $val;
                    $this->valueObj = null;
                }
        }
    }

    /**
     * @return String ISO 8601 date, suitable for insertion into database
     */
    function dataValue() {
        if($this->valueObj) {
            return $this->valueObj->toString('yyyy-MM-dd');
        } else {
            return null;
        }
    }
}