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

class GoogleSiteMapExtension extends DataExtension {

    private static $db = array(
        "Priority" => "Varchar(5)"
    );

    public function updateSettingsFields(&$fields) {
        $prorities = array(
            '-1' => _t('GoogleSitemaps.PRIORITYNOTINDEXED', "Not indexed"),
            '1.0' => '1 - ' . _t('GoogleSitemaps.PRIORITYMOSTIMPORTANT', "Most important"),
            '0.9' => '2',
            '0.8' => '3',
            '0.7' => '4',
            '0.6' => '5',
            '0.5' => '6',
            '0.4' => '7',
            '0.3' => '8',
            '0.2' => '9',
            '0.1' => '10 - ' . _t('GoogleSitemaps.PRIORITYLEASTIMPORTANT', "Least important")
        );
        $tabset = $fields->findOrMakeTab('Root.Settings');
        $message = "<p>";
        $message .= sprintf(_t('GoogleSitemaps.METANOTEPRIORITY', "Manually specify a Google Sitemaps priority for this page (%s)"),
            '<a href="http://www.google.com/support/webmasters/bin/answer.py?hl=en&answer=71936#prioritize" target="_blank">?</a>'
        );
        $message .= "</p>";
        $tabset->push(new Tab('GoogleSitemap', _t('GoogleSitemaps.TABGOOGLESITEMAP', 'Google Sitemap'),
            new LiteralField("GoogleSitemapIntro", $message),
            $priority = new DropdownField("Priority", $this->owner->fieldLabel('Priority'), $prorities, $this->owner->Priority)
        ));
        $priority->setEmptyString(_t('GoogleSitemaps.PRIORITYAUTOSET', 'Auto-set based on page depth'));
    }

    public function updateFieldLabels(&$labels) {
        parent::updateFieldLabels($labels);
        $labels['Priority'] = _t('GoogleSitemaps.METAPAGEPRIO', "Page Priority");
    }

    /**
     * @return void
     */
    public function onAfterPublish() {
        PublisherSubscriberManager::getInstance()->publish('dataobject_publish', array($this));
    }
    /**
     * @return void
     */
    public function onAfterUnpublish() {
        PublisherSubscriberManager::getInstance()->publish('dataobject_unpublish', array($this));
    }

    public function getGooglePriority() {
        $field = $this->owner->hasField('Priority');
        if(isset($this->Priority) || ($field && $this->Priority = $this->owner->getField('Priority'))) {
            return ($this->Priority < 0) ? false : $this->Priority;
        }
        return false;
    }

    public function getChangeFrequency() {
        $date = date('Y-m-d H:i:s');
        $created = new SS_Datetime();
        $created->value = ($this->owner->Created) ? $this->owner->Created : $date;
        $now = new SS_Datetime();
        $now->value = $date;
        $versions = ($this->owner->Version) ? $this->owner->Version : 1;
        $timediff = $now->format('U') - $created->format('U');
        // Check how many revisions have been made over the lifetime of the
        // Page for a rough estimate of it's changing frequency.
        $period = $timediff / ($versions + 1);
        if ($period > 60 * 60 * 24 * 365) {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_YEARLY;
        } elseif ($period > 60 * 60 * 24 * 30) {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_MONTHLY;
        } elseif ($period > 60 * 60 * 24 * 7) {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_WEEKLY;
        } elseif ($period > 60 * 60 * 24) {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_DAILY;
        } elseif ($period > 60 * 60) {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_HOURLY;
        } else {
            $freq = GoogleSiteMapGenerator::CHANGE_FREQ_ALWAYS;
        }
        return $freq;
    }

    public function canIncludeInGoogleSiteMap() {
        $can = true;
        if($this->owner->hasMethod('AbsoluteLink')) {
            $hostHttp = parse_url(Director::protocolAndHost(), PHP_URL_HOST);
            $objHttp = parse_url($this->owner->AbsoluteLink(), PHP_URL_HOST);
            if($objHttp != $hostHttp) {
                $can = false;
            }
        }
        return $can;
    }
}