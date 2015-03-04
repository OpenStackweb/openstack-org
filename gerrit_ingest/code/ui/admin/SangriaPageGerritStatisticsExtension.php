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

class SangriaPageGerritStatisticsExtension extends Extension {

    public function onBeforeInit(){
        Config::inst()->update(get_class($this), 'allowed_actions', array('GerritStatisticsReport'));
        Config::inst()->update(get_class($this->owner), 'allowed_actions', array('GerritStatisticsReport'));

        Requirements::block(SAPPHIRE_DIR . "/javascript/jquery_improvements.js");
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.js');
        Requirements::block(FRAMEWORK_DIR . '/thirdparty/jquery/jquery.min.js');
        Requirements::block(THIRDPARTY_DIR . '/jquery-cookie/jquery.cookie.js');

        if(Director::isLive()) {
            Requirements::javascript('themes/openstack/javascript/jquery.min.js');
        }
        else{
            Requirements::javascript('themes/openstack/javascript/jquery.js');
        }

        Requirements::javascript('themes/openstack/javascript/jquery-migrate-1.2.1.min.js');

        Requirements::javascript("themes/openstack/javascript/jquery.cookie.js");
        Requirements::javascript("themes/openstack/javascript/bootstrap.min.js");
        Requirements::css('themes/openstack/css/bootstrap.min.css');
        Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/markerclusterer.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/oms.min.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/infobubble-compiled.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/google.maps.jquery.js");
        Requirements::javascript('themes/openstack/javascript/Chart.js');
        Requirements::javascript("gerrit_ingest/js/sangria.page.gerrit.statistics.report.js");
        Requirements::css('gerrit_ingest/css/sangria.page.gerrit.statistics.report.css');
    }

    public function CommitsPerUser(){
        SangriaPage_Controller::generateDateFilters('', 'CreatedDate');
        $date_filter = SangriaPage_Controller::$date_filter_query;

        $sql = <<< SQL
SELECT C.Commits, M.Email
FROM ( SELECT COUNT(ID) Commits , MemberId FROM GerritChangeInfo WHERE {$date_filter} GROUP BY MemberID) AS C
INNER JOIN Member M on M.ID = C.MemberId
ORDER BY C.Commits DESC
SQL;

        $res = DB::query($sql);

        $list = new ArrayList();
        foreach($res as $row){
            $list->add(new ArrayData($row));
        }
        return $list;
    }

    public function CommitsPerCountry(){

        SangriaPage_Controller::generateDateFilters('C', 'CreatedDate');
        $date_filter = SangriaPage_Controller::$date_filter_query;

        $sql = <<< SQL
SELECT COUNT(C.ID) AS Commits , M.Country FROM GerritChangeInfo C INNER JOIN Member M on M.ID = C.MemberId
WHERE {$date_filter}
GROUP BY M.Country ORDER BY Commits DESC;
SQL;

        $res = DB::query($sql);

        $list = new ArrayList();
        foreach($res as $row){
            $country_code = $row['Country'];
            $country_name = isset(CountryCodes::$iso_3166_countryCodes[$country_code])? CountryCodes::$iso_3166_countryCodes[$country_code]:'NOT SET';
            $country_code = isset(CountryCodes::$iso_3166_countryCodes[$country_code])? $country_code:'NOT SET';
            $list->add(new ArrayData( array(
                'Commits'    => $row['Commits'],
                'CountryName' => $country_name,
                'Country'     => $country_code)));
        }
        return $list;
    }

    public function TotalCommits(){
        SangriaPage_Controller::generateDateFilters('C', 'CreatedDate');
        $date_filter = SangriaPage_Controller::$date_filter_query;

        $sql = <<< SQL
SELECT COUNT(C.ID) AS Commits FROM GerritChangeInfo C WHERE {$date_filter};
SQL;

        $res = DB::query($sql);
        return $res->value();
    }

    public function UsersWithCommits(){
        SangriaPage_Controller::generateDateFilters('', 'CreatedDate');
        $date_filter = SangriaPage_Controller::$date_filter_query;

        $sql = <<< SQL
SELECT COUNT(M.ID)
FROM ( SELECT COUNT(ID) Commits , MemberId FROM GerritChangeInfo WHERE {$date_filter} GROUP BY MemberID) AS C
INNER JOIN Member M on M.ID = C.MemberId;
SQL;

        $res = DB::query($sql);
        return $res->value();
    }

    public function getQuickActionsExtensions(&$html){
        $view = new SSViewer('SangriaPage_GerritStatisticsLinks');
        $html .= $view->process($this->owner);
    }

}