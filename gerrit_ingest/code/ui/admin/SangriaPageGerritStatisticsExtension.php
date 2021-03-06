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
    }

    public function GerritStatisticsReport(){

        JQueryCoreDependencies::renderRequirements();
        BootstrapDependencies::renderRequirements();
        GoogleMapScriptBuilder::renderMarkersClustered();
        Requirements::css('gerrit_ingest/css/sangria.page.gerrit.statistics.report.css');
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript('themes/openstack/javascript/Chart.js');
        Requirements::javascript("gerrit_ingest/js/sangria.page.gerrit.statistics.report.js");
        return $this->owner->getViewer('GerritStatisticsReport')->process($this->owner);
    }


    public function CommitsPerUser(){
        SangriaPage_Controller::generateDateFilters('', 'CreatedDate');
        $date_filter = SangriaPage_Controller::$date_filter_query;

        $sql = <<< SQL
SELECT C.Commits, M.Email
FROM ( SELECT COUNT(ID) Commits , OwnerID FROM GerritChangeInfo WHERE {$date_filter} GROUP BY OwnerID) AS C
INNER JOIN GerritUser M on M.ID = C.OwnerID
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
SELECT COUNT(C.ID) AS Commits , IFNULL(M.Country, 'N/A') AS Country FROM GerritChangeInfo C 
INNER JOIN GerritUser U on U.ID = C.OwnerID
LEFT JOIN Member M on M.ID = U.MemberID
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
FROM ( SELECT COUNT(ID) Commits , OwnerID FROM GerritChangeInfo WHERE {$date_filter} GROUP BY OwnerID) AS C
INNER JOIN GerritUser M on M.ID = C.OwnerID;
SQL;

        $res = DB::query($sql);
        return $res->value();
    }

}