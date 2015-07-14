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
/**
 * Class SapphireSummitSectionService
 */

final class SapphireSummitPageSectionService implements ISapphireSummitPageSectionService {

    public function getSummitSectionService($pageId) {
        $summitPageSectionSettings = SummitPageSectionSettings::get()->where("SummitPageId = {$pageId}")->sort("Order");

        $summitPageSectionSettingsArray = new ArrayList();

        foreach($summitPageSectionSettings as $item){
            $summitSection = new SummitPageSection();
            $summitSection->CssClass = $this->getCssClass($item);
            $summitSection->Hash = $this->getHash($item);
            $summitSection->Text = $item->Name;
            $summitSection->Style = $this->getStyle($item);
            if ($this->relatedFieldHasValue($item)) {
                $summitPageSectionSettingsArray->add($summitSection);
            }
        }

        return $summitPageSectionSettingsArray;
    }

    function getCssClass($item) {
        if ($item->Name == 'Venue') {
            $cssClass = 'fa fa-map-marker';
        }
        else if ($item->Name == 'Hotels & Airport') {
            $cssClass = 'fa fa-h-square';
        }
        else if ($item->Name == 'Getting Around') {
            $cssClass = 'fa fa-road';
        }
        else if ($item->Name == 'Travel Support Program') {
            $cssClass = 'fa fa-globe';
        }
        else if ($item->Name == 'Visa Info') {
            $cssClass = 'fa fa-plane';
        }
        else if ($item->Name == 'Locals') {
            $cssClass = 'fa fa-heart';
        }
        return $cssClass;
    }

    function relatedFieldHasValue($item) {
        if ($item->Name == 'Getting Around') {
            $hasValue = strlen($item->SummitPage()->GettingAround) > 0;
        }
        else if ($item->Name == 'Travel Support Program') {
            $hasValue = strlen($item->SummitPage()->TravelSupport) > 0;
        }
        else if ($item->Name == 'Visa Info') {
            $hasValue = strlen($item->SummitPage()->VisaInformation) > 0;
        }
        else if ($item->Name == 'Locals') {
            $hasValue = strlen($item->SummitPage()->Locals) > 0;
        }
        else {
            $hasValue = true;
        }
        return $hasValue;
    }

    function getHash($item) {
        if ($item->Name == 'Venue') {
            $hash = 'venue';
        }
        else if ($item->Name == 'Hotels & Airport') {
            $hash = 'hotels';
        }
        else if ($item->Name == 'Getting Around') {
            $hash = 'getting-around';
        }
        else if ($item->Name == 'Travel Support Program') {
            $hash = 'travel-support';
        }
        else if ($item->Name == 'Visa Info') {
            $hash = 'visa';
        }
        else if ($item->Name == 'Locals') {
            $hash = 'locals';
        }
        return $hash;
    }

    function getStyle($item) {
        if ($item->BackgroundImage()->ID > 0) {
            $style = "background:url({$item->BackgroundImage()->Filename}) no-repeat; background-size: 100% auto;";
        }
        else if ($item->BackgroundColor()->ID > 0) {
            $style = "background-color:#{$item->BackgroundColor()->Color}";
        }
        else {
            $style = "";
        }
        return $style;
    }
}