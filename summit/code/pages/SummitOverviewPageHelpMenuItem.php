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

class SummitOverviewPageHelpMenuItem extends DataObject {

    private static $db = array(
      'Label'  => 'Text',
      'Url'    => 'Text',
       //http://fortawesome.github.io/Font-Awesome/icons/
       'FAIcon' => "Enum('fa-h-square, fa-comment, fa-tag, fa-question, fa-users, fa-mobile, none, fa-map-signs, fa-map, fa-calendar, fa-bed, fa-beer, fa-cab, fa-compass, fa-cutlery, fa-location-arrow, fa-venus, fa-youtube-play','none')",
       'Order'  => "Int",
    );

    private static $has_one = array(
        'Owner'     => 'SummitOverviewPage',
    );
}