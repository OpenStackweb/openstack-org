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
class SummitConfirmSpeakerPage extends SummitPage
{

    static $defaults = array(
        'ShowInMenus' => false
    );
}

/**
 * Class SummitConfirmSpeakerPage_Controller
 */
class SummitConfirmSpeakerPage_Controller extends SummitPage_Controller
{

    static $allowed_actions = array(
        'OnsitePhoneForm',
        'doSavePhoneNumber',
        'confirm',
        'Thanks'
    );

    public function init()
    {
        parent::init();
    }

    public function index()
    {
        $request = Session::get('Current.PresentationSpeakerSummitAssistanceConfirmationRequest');
        if(is_null($request))
        {
            $response = new SS_HTTPResponse;
            $response->setStatusCode(404);
            return $response;
        }
        return $this->getViewer('index')->process($this);
    }

    public function confirm()
    {
        parent::init();

        try {

            $token = Session::get('SummitConfirmSpeakerPage.Token');

            if (isset($_REQUEST['t'])) {
                $token = base64_decode($_REQUEST['t']);
                Session::set('SummitConfirmSpeakerPage.Token', $token);
                return $this->redirect($this->Link('confirm'));
            }

            if(empty($token))
                throw new InvalidArgumentException('missing token!');

            $request = PresentationSpeakerSummitAssistanceConfirmationRequest::get()
                ->filter('ConfirmationHash',
                    PresentationSpeakerSummitAssistanceConfirmationRequest::HashConfirmationToken($token))
                ->first();


            if (is_null($request)) {
                throw new NotFoundEntityException('PresentationSpeakerSummitAssistanceConfirmationRequest','');
            }

            if(!$request->alreadyConfirmed()) {
                $request->confirm($token);
                $request->write();
            }

            $data['Speaker'] = $request->Speaker();
            $data['Summit']  = $request->Summit();
            Session::set('Current.PresentationSpeakerSummitAssistanceConfirmationRequest', $request);
            return $this->customise($data)->renderWith(array('SummitConfirmSpeakerPage', 'SummitPage'), $this->parent);
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->httpError(404, 'Sorry, this speaker confirmation token does not seem to be correct.');
        }
    }

    public function OnSitePhoneForm()
    {
        $request = Session::get('Current.PresentationSpeakerSummitAssistanceConfirmationRequest');
        $form = new OnsitePhoneForm($this, 'OnSitePhoneForm', $request);
        $form->loadDataFrom($request);
        return $form;
    }
}