<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class RSVPSingleValueValidationRule
 */
class RSVPSingleValueValidationRule
    extends DataObject
    implements IRSVPSingleValueValidationRule {

    private static $singular_name = 'RSVP Validation Rule';

    static $db = array(
        'Name'    => 'VarChar(255)',
        'Message' => 'Text',
    );

    static $indexes = array(
        'Name' => array('type'=>'unique', 'value'=>'Name')
    );

    static $has_one = array(
    );


    static $belongs_to = array(
    );

    static $many_many = array(
    );

    static $has_many = array(
    );

    private static $defaults = array(
    );

    /**
     * @return string
     */
    public function name()
    {
        return $this->getField('Name');
    }

    /**
     * @return array()
     */
    public function getHtml5Attributes()
    {
        $this->registerJS();
        return array();
    }

    /**
     * @return bool
     */
    public function hasCustomMessage()
    {
        $message  = $this->getField('Message');
        return !empty($message);
    }

    /**
     * @return string
     */
    public function customMessage()
    {
        return $this->getField('Message');
    }

    private function registerJS(){

        Requirements::block(SAPPHIRE_DIR . '/thirdparty/behaviour/behaviour.js');
        Requirements::block(SAPPHIRE_DIR . '/thirdparty/prototype/prototype.js');
        Requirements::block(SAPPHIRE_DIR . '/javascript/prototype_improvements.js');
        Requirements::block(SAPPHIRE_DIR . "/javascript/jquery_improvements.js");
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.min.js');
        Requirements::block(THIRDPARTY_DIR . '/jquery-cookie/jquery.cookie.js');

        if(Director::isLive()) {
            Requirements::javascript('themes/openstack/javascript/jquery.min.js');
        }
        else{
            Requirements::javascript('themes/openstack/javascript/jquery.js');
        }

        Requirements::javascript('themes/openstack/javascript/jquery-migrate-1.2.1.min.js');

        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/additional-methods.js");

    }

    protected function validate() {
        $valid = parent::validate();
        if(!$valid->valid()) return $valid;

        $class = get_class($this);
        $res = $class::get()->filter( array('Name' => $this->Name ))->count();
        if($res > 0 ){
            return $valid->error('There is already another validation rule with that name!');
        }
        return $valid;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
       return (int)$this->getField('ID');
    }
}