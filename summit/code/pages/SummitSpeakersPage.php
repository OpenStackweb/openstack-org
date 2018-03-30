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

class SummitSpeakersPage extends SummitPage
{

}

class SummitSpeakersPage_Controller extends SummitPage_Controller {

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @param ISpeakerRepository $speaker_repository
     */
    public function setSpeakerRepository(ISpeakerRepository $speaker_repository){
        $this->speaker_repository = $speaker_repository;
    }

    public function init()
    {
        $this->top_section = 'full';
        parent::init();
        Requirements::block('summit/css/combined.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.css');
        Requirements::css('summit/css/summit-speakers-page.css');
        Requirements::javascript('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.js');
    }

    /**
     * @return ArrayList
     */
    public function getAllSpeakers(){

        list($page, $page_size, $count, $data) = $this->speaker_repository->searchBySummitSchedulePaginated
        (
            $this->Summit(),1, PHP_INT_MAX, '','fullname');

        return new ArrayList($data);
    }

    public function getNavLetters(){
        $elements = range('A', 'Z');
        $alphas = new ArrayList();
        foreach ($elements as $char){
            $alphas->add(new ArrayData(['Char' => $char]));
        }
        return $alphas;
    }

    public function getScheduleGlobalSearchPageLink($term){
        $page = SummitAppSchedPage::getBy($this->Summit());
        if(is_null($page)) return '#';

        return sprintf('%sglobal-search?t=%s',$page->getAbsoluteLiveLink(false), urldecode($term));
    }
}