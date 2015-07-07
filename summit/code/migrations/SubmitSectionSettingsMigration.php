<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 7/6/15
 * Time: 7:59 PM
 */

class SubmitSectionSettingsMigration extends MigrationTask
{

    protected $title = "Add section settings to submit pages";

    protected $description = "Add section settings to submit pages";

    function up()
    {
        echo "Starting Migration Proc ...<BR>";
        //check if migration already had ran ...
        $migration = Migration::get()->filter('Name', $this->title)->first();

        if (!$migration) {

            $template_sections = array("Visa Information", "Getting Around", "Venue", "Hotels & Airports", "Travel Support");
            $submitLocationPages = SummitLocationPage::get();

            foreach ($submitLocationPages as $submitLocationPage) {
                if ($submitLocationPage->SectionsSettings()->Count() == 0) {
                    $order = 0;
                    foreach ($template_sections as $section_name) {
                        $sectionSettings = new SummitPageSectionSettings();
                        $sectionSettings->Name = $section_name;
                        $sectionSettings->Order = $order;
                        $submitLocationPage->SectionsSettings()->add($sectionSettings);
                        $order++;
                    }
                    $submitLocationPage->write();
                }
            }

            $migration = new Migration();
            $migration->Name = $this->title;
            $migration->Description = $this->description;
            $migration->Write();
        }
        echo "Ending  Migration Proc ...<BR>";
    }

    function down()
    {

    }
}