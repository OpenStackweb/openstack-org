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
 * Class SpeakerPresentationLink
 */
class SpeakerPresentationLink
    extends DataObject
    implements ISpeakerPresentationLink {

    private static $db = array(
        'LinkUrl' => 'Text',
        'Title'   => 'Text'
    );

    private static $summary_fields = array
    (
        'LinkUrl' => 'Url',
        'Title'   => 'Title',
    );

    private static $has_one = array
    (
        'Speaker' => 'PresentationSpeaker',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getYoutubeID() {
        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $this->LinkUrl, $matches);
        if (count($matches) > 1) {
            return $matches[1];
        }

        return '';
    }
}