<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class SpeakerSelectionAnnouncementEmailCreationRequestSenderServiceFactory
 */
final class SpeakerSelectionAnnouncementEmailCreationRequestSenderServiceFactory
{
    /**
     * @param SpeakerSelectionAnnouncementEmailCreationRequest $request
     * @return null|IMessageSenderService
     */
    public static function build(SpeakerSelectionAnnouncementEmailCreationRequest $request){
        switch ($request->Type){
            case IPresentationSpeaker::AnnouncementEmailAccepted:{
                return new PresentationSpeakerAcceptedAnnouncementEmailSender();
            }
            break;
            case IPresentationSpeaker::AnnouncementEmailAcceptedAlternate:{
                return new PresentationSpeakerAcceptedAlternateAnnouncementEmailSender();
            }
                break;
            case IPresentationSpeaker::AnnouncementEmailAcceptedRejected:{
                return new PresentationSpeakerAcceptedRejectedAnnouncementEmailSender();
            }
                break;
            case IPresentationSpeaker::AnnouncementEmailAlternate:{
                return new PresentationSpeakerAlternateAnnouncementEmailSender();
            }
                break;
            case IPresentationSpeaker::AnnouncementEmailAlternateRejected:{
                return new PresentationSpeakerAlternateRejectedAnnouncementEmailSender();
            }
            break;
        }
        return null;
    }
}