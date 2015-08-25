<?php
/**
 * Copyright 2014 Openstack Foundation
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

class SummitConfirmSpeakerPage extends SummitPage {    

    static $defaults = array(
        'ShowInMenus' => false
    );

}


class SummitConfirmSpeakerPage_Controller extends SummitPage_Controller {

    static $allowed_actions = array(
        'OnsitePhoneForm',
        'doSavePhoneNumber',
        'confirm',
        'Thanks'
    );    
    
	public function init() {
		parent::init();
	}    

    public function confirm() {
        parent::init();

        $getVars = $this->request->getVars();
        if (isset($hashKey)) $speakerID = substr(base64_decode($hashKey), 3);
        if (isset($hashKey)) $speakerID = $hashKey;


        if (isset($speakerID) && is_numeric($speakerID) && $Speaker = PresentationSpeaker::get()->byID($speakerID)) {

            Session::set('ConfirmSpeakerHash', $hashKey);
            Session::set('Speaker', $Speaker);


            $Speaker->Confirmed = TRUE;
            $Speaker->write();

            $data['FirstName'] = $Speaker->FirstName;
            $data['LastName'] = $Speaker->LastName;
            $data['Summit'] = Summit::get_active();

            return $this->customise($data)
                ->renderWith(array('SummitConfirmSpeakerPage','SummitPage'), $this->parent);        


        } else {
            return $this->httpError(404, 'Sorry, this speaker confirmation code does not seem to be correct.');
        }

    }   

    public function OnsitePhoneForm() {

        $speakerHash = Session::get('ConfirmSpeakerHash');
        $speaker = Session::get('Speaker');
        $OnsitePhoneForm = new OnsitePhoneForm($this, 'OnsitePhoneForm', $speakerHash);
        $OnsitePhoneForm->loadDataFrom($speaker);

        return $OnsitePhoneForm;

    }


}