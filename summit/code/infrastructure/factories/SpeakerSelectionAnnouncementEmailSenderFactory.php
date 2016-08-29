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
final class SpeakerSelectionAnnouncementEmailSenderFactory implements ISpeakerSelectionAnnouncementSenderFactory
{
    /**
     * @param ISummit $summit
     * @param IPresentationSpeaker $speaker
     * @param string $role
     * @return PresentationSpeakerAcceptedAnnouncementEmailSender
     * @throws Exception
     */
    public function build(ISummit $summit, IPresentationSpeaker $speaker, $role = IPresentationSpeaker::RoleSpeaker)
    {

        $has_published = $speaker->hasPublishedPresentations($summit->getIdentifier(), $role);
        $has_rejected  = $speaker->hasRejectedPresentations($summit->getIdentifier(), $role);
        $has_alternate = $speaker->hasAlternatePresentations($summit->getIdentifier(), $role);

        if($has_published && !$has_rejected && !$has_alternate)
            return new PresentationSpeakerAcceptedAnnouncementEmailSender;

        if(!$has_published && !$has_rejected && $has_alternate)
            return new PresentationSpeakerAlternateAnnouncementEmailSender;

        if(!$has_published && $has_rejected && !$has_alternate)
            return new PresentationSpeakerRejectedAnnouncementEmailSender;

        if($has_published && !$has_rejected && $has_alternate)
            return new PresentationSpeakerAcceptedAlternateAnnouncementEmailSender;

        if($has_published && $has_rejected && !$has_alternate)
            return new PresentationSpeakerAcceptedRejectedAnnouncementEmailSender;

        if(!$has_published && $has_rejected && $has_alternate)
            return new PresentationSpeakerAlternateRejectedAnnouncementEmailSender;

        if($has_published && $has_rejected && $has_alternate)
            return new PresentationSpeakerAcceptedAlternateAnnouncementEmailSender;

        return null;
    }
}