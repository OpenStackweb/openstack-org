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
 * Class Paper
 */
final class Paper extends DataObject
{
    static $db = [
        'Title'   => 'Text',
        'Subtitle' => 'Text',
        'Abstract' => 'HTMLText',
        'Footer'    => 'HTMLText',
    ];

    public function setAbstract($value){
        $value = preg_replace ( "/<p[^>]*?>(.*)<\/p>/" , "$1" , $value );
        $this->setField('Abstract', $value);
    }

    public function setFooter($value){
        $value = preg_replace ( "/<p[^>]*?>(.*)<\/p>/" , "$1" , $value );
        $this->setField('Footer', $value);
    }

    static $has_one = [
        'Creator' => 'Member',
        'UpdatedBy' => 'Member',
        'BackgroundImage' => 'CloudImage',
    ];

    static $has_many = [
        'Sections' => 'PaperSection',
        'Translators' => 'PaperTranslator',
    ];

    public function onBeforeWrite(){
        parent::onBeforeWrite();
        if($this->ID == 0){
            $this->CreatorID = Member::currentUserID();
        }

        $this->UpdatedByID = Member::currentUserID();
    }

    public function getBackgroundImageUrl(){
        $url =  'papers/img/default_bg.jpg';
        if($this->BackgroundImage()->exists()){
            $url = $this->BackgroundImage()->Url;
        }

        return Director::absoluteURL($url);
    }

    public function getOrderedSections(){
        return $this->Sections()->where('ParentSectionID = 0')->sort('Order','ASC');
    }

    public function getFirstSection(){
        return $this->Sections()->where('ParentSectionID = 0')->sort('Order','ASC')->first();
    }

    public function getI18nContext(){
        $slug = singleton('SiteTree')->generateURLSegment($this->Title);
        return $slug;
    }

    public function getTranslatorsByCurrentLocale(){
        $current_locale = GetTextTemplateHelpers::_currentLocale();
        return $this->Translators()->filter('LanguageCode', $current_locale);
    }

    protected function validate()
    {
        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }

        $title = trim($this->Title);
        if (strlen($title) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Title is too long!');
        }

        $sub_title = trim($this->Subtitle);
        if (strlen($sub_title) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Subtitle is too long!');
        }

        $abstract = trim($this->Abstract);
        if (strlen($abstract) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Abstract is too long!');
        }

        $footer = trim($this->Footer);
        if (strlen($footer) > GetTextTemplateHelpers::MAX_MSG_ID_LEN) {
            return $valid->error('Footer is too long!');
        }

        return $valid;
    }
}