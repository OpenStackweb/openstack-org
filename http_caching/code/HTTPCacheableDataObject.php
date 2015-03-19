<?php
/**
 * Copyright 2015 Openstack Foundation
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
 * Class HTTPCacheableDataObject
 */
class HTTPCacheableDataObject extends DataExtension {

    private static $db = array(
        'MaxAge' => 'Int'
    );

    /**
     * Extension point for the CachingPolicy.
     */
    public function getCacheAge($cacheAge) {
        if (!is_null($this->owner->MaxAge)) {
            return (int)($this->owner->MaxAge*60);
        }
    }

    public function updateCMSFields(FieldList $fields) {
// Only admins are allowed to modify this.
        $member = Member::currentUser();
        if (!$member || !Permission::checkMember($member, 'ADMIN')) {
            return;
        }
        $fields->addFieldsToTab('Root.Caching', array(
            new LiteralField('Instruction', '<p>The following field controls the length of time the page will ' .
                'be cached for. You will not be able to see updates to this page for at most the specified ' .
                'amount of minutes. Leave empty to set back to the default configured for your site. Set ' .
                'to 0 to explicitly disable caching for this page.</p>'),
            new TextField('MaxAge', 'Custom cache timeout [minutes]')
        ));
    }

    function getModificationTimestamp(){
        $timestamp = strtotime($this->owner->LastEdited);
        return $timestamp;
    }

    function getEtag(){
        $etag = md5(sprintf('%s_%s_%s',$this->owner->ID, $this->owner->ClassName, $this->owner->LastEdited));
        return $etag;
    }
}