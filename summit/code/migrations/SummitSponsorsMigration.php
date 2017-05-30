<?php

/**
 * Copyright 2016 OpenStack Foundation
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
final class SummitSponsorsMigration extends AbstractDBMigrationTask
{
    protected $title = "SummitSponsorsMigration";

    protected $description = "SummitSponsorsMigration";

    function doUp()
    {
        global $database;

        $sponsor_types = array(
            'Headline' => 'Headline Sponsors',
            'Premier' => 'Premier Sponsors',
            'Spotlight' => 'Spotlight Sponsors',
            'Exhibitor' => 'Exhibitor Sponsors',
            'Event' => 'Event Sponsors',
            'Startup' => 'Startup Sponsors',
            'InKind' => 'Community Sponsors',
            'Media' => 'Media Sponsors',
            'Party' => 'Party Sponsors'
        );

        $order = 1;

        foreach($sponsor_types as $type => $label) {
            if (SponsorshipType::get()->filter('Name',$type)->count() == 0) {
                $sponsor_type = new SponsorshipType();
                $sponsor_type->Name = $type;
                $sponsor_type->Order = $order;
                $sponsor_type->Label = $label;
                $sponsor_type->write();
            }
            $order ++;
        }

        $sponsors = DB::query("SELECT * FROM SummitSponsorPage_Companies");

        foreach ($sponsors as $sponsor) {
            $sponsor_exists = Sponsor::get()
                        ->filter(array('SponsorPageID' => $sponsor['SummitSponsorPageID'], 'CompanyID' => $sponsor['CompanyID']))
                        ->count();

            if (!$sponsor_exists) {
                $new_sponsor = new Sponsor();
                $new_sponsor->CompanyID = $sponsor['CompanyID'];
                $new_sponsor->SponsorPageID = $sponsor['SummitSponsorPageID'];
                $new_sponsor->SubmitPageUrl = $sponsor['SubmitPageUrl'];
                $new_sponsor->SummitID = $sponsor['SummitID'];
                $sponsorship_type = SponsorshipType::get()->filter('Name',$sponsor['SponsorshipType'])->first();
                if ($sponsorship_type) {
                    $new_sponsor->SponsorshipTypeID = $sponsorship_type->ID;
                }
                $new_sponsor->write();

            }
        }

    }

    function doDown()
    {
        // TODO: Implement doDown() method.
    }
}