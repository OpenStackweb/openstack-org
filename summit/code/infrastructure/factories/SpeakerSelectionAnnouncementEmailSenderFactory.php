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
     * @return IMessageSenderService
     * @throws Exception
     */
    public function build(ISummit $summit, IPresentationSpeaker $speaker, $role = IPresentationSpeaker::RoleSpeaker)
    {
        $has_published = $speaker->hasPublishedRegularPresentations($summit->getIdentifier(), $role, true, $summit->getExcludedTracksForPublishedPresentations());
        $has_rejected  = $speaker->hasRejectedPresentations($summit->getIdentifier(), $role, true, $summit->getExcludedTracksForRejectedPresentations());
        $has_alternate = $speaker->hasAlternatePresentations($summit->getIdentifier(), $role, true, $summit->getExcludedTracksForAlternatePresentations(), true);

        echo sprintf(
            "speaker %s (%s) got following flags has_published %b has_rejected %b has_alternate %b",
            $speaker->getEmail(),
            $speaker->ID,
            $has_published,
            $has_rejected,
            $has_alternate
        ).PHP_EOL;

        if($has_published && !$has_rejected && !$has_alternate) {

            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-accepted-only email",
                $speaker->getEmail(),
                $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAcceptedAnnouncementEmailSender();
        }

        if(!$has_published && !$has_rejected && $has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-alternate-only email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAlternateAnnouncementEmailSender();
        }

        if(!$has_published && $has_rejected && !$has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-rejected-only email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerRejectedAnnouncementEmailSender();
        }

        if($has_published && !$has_rejected && $has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-accepted-alternate email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAcceptedAlternateAnnouncementEmailSender();
        }

        if($has_published && $has_rejected && !$has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-accepted-rejected email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAcceptedRejectedAnnouncementEmailSender();
        }

        if(!$has_published && $has_rejected && $has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-alternate-rejected email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAlternateRejectedAnnouncementEmailSender();
        }

        if($has_published && $has_rejected && $has_alternate) {
            echo sprintf(
                "speaker %s (%s) will get a presentation-speaker-accepted-alternate email",
                $speaker->getEmail(),
                    $speaker->ID
            ).PHP_EOL;
            return new PresentationSpeakerAcceptedAlternateAnnouncementEmailSender();
        }

        echo sprintf(
            "speaker %s (%s) will not get an email",
            $speaker->getEmail(),
            $speaker->ID
        );
        return null;
    }
}