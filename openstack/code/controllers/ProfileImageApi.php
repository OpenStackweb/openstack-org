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
final class ProfileImageApi extends AbstractRestfulJsonApi
{

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    protected function authenticate()
    {
        return true;
    }

    static $url_handlers = array(
        'GET speakers/$SpeakerID!' => 'getSpeakerProfileImage',
        'GET members/$MemberID!'   => 'getMemberProfileImage',
    );

    static $allowed_actions = array
    (
        'getSpeakerProfileImage',
        'getMemberProfileImage',
    );


    public function getSpeakerProfileImage(SS_HTTPRequest $request)
    {
        $speaker_id  = intval($request->param('SpeakerID'));
        $speaker     = PresentationSpeaker::get()->byID($speaker_id);
        if(is_null($speaker)) return $this->notFound();
        $photo_url   = $speaker->ProfilePhoto();
        $body        = file_get_contents($photo_url);
        $ext         = 'jpg';
        $response = new SS_HTTPResponse($body,200);
        $response->addHeader('Content-Type', 'image/'.$ext);
        return $response;
    }

    public function getMemberProfileImage(SS_HTTPRequest $request)
    {
        $member_id  = intval($request->param('MemberID'));
        $member     = Member::get()->byID($member_id);
        if(is_null($member)) return $this->notFound();
        $photo_url   = $member->ProfilePhotoUrl($width = 100, $generic_photo_type = 'speaker');
        $body        = file_get_contents($photo_url);
        $ext         = 'jpg';
        $response = new SS_HTTPResponse($body,200);
        $response->addHeader('Content-Type', 'image/'.$ext);
        return $response;
    }
}