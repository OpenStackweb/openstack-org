<?php


class SummitAboutPage extends SummitPage {

    private static $db = array();

    private static $many_many = array(
        'PageSections' => 'PageSection'
    );

    private static $many_many_extraFields = array(
        'PageSections' => array(
            'Order' => 'Int',
        )
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $config = GridFieldConfig_RecordEditor::create();
        $config->removeComponentsByType('GridFieldAddNewButton');
        $multi_class_selector = new GridFieldAddNewMultiClass();

        $multi_class_selector->setClasses(
            array
            (
                'PageSectionText'       => 'Just Text' ,
                'PageSectionPicture'    => 'Text & Picture' ,
                'PageSectionLinks'      => 'Links' ,
                'PageSectionSpeakers'   => 'Speakers' ,
                'PageSectionSponsors'   => 'Sponsors' ,
                'PageSectionVideos'     => 'Videos' ,
                'PageSectionMovement'   => 'Join Movement'
            )
        );

        $config->addComponent($multi_class_selector);
        $config->addComponent(new GridFieldSeedDefaultSummitPageSections);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $gridField = new BetterGridField('PageSections', 'PageSections', $this->PageSections(), $config);
        $fields->addFieldToTab('Root.Main', $gridField);

        return $fields;
    }

    function seedDefaultPageSections() {
        $previousPages = SummitAboutPage::get()->filter('ID:not', $this->ID)->sort('Created', 'DESC');
        $lastPage = null;
        foreach ($previousPages as $page) {
            if ($page->PageSections()->Exists()) {
                $lastPage = $page;
            }
        }

        if ($lastPage) {
            $this->PageSections()->setByIDList($lastPage->getIDList());
        }
    }
}


class SummitAboutPage_Controller extends SummitPage_Controller {

    public function init()
    {
        $this->top_section = 'full';
        parent::init();
        Requirements::block('summit/css/combined.css');
        Requirements::css('themes/openstack/static/css/combined.css');
        Requirements::css('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.css');
        Requirements::javascript('themes/openstack/javascript/secondary-nav.jquery/secondary-nav.jquery.js');
        Requirements::javascript('summit/javascript/summit-about-page.js');
    }

    public function getNavSections() {
        return $this->PageSections()->filter(['ShowInNav' => 1, 'Enabled' => 1])->sort('Order');
    }

    public function getPageSections() {
        return $this->PageSections()->filter('Enabled', 1)->sort('Order');
    }
}