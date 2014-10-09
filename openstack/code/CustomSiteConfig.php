<?php
  
class CustomSiteConfig extends DataExtension {

	private static $db=  array(
		'DisplaySiteBanner' => 'Boolean'
	);
	private static $has_many = array('SiteBannerConfigurationSettings'=>'SiteBannerConfigurationSetting');
  
    public function updateCMSFields(FieldList $fields) {
	    $config = GridFieldConfig_RelationEditor::create(10);

        $settings = new GridField('SiteBannerConfigurationSettings','SiteBannerConfigurationSetting', SiteBannerConfigurationSetting::get(),$config);
	    $settings->getConfig()->getComponentByType('GridFieldDataColumns')->setDisplayFields(
		    array(
			    'SiteBannerMessage' => 'Message',
			    'SiteBannerRank'    => 'Rank',
			    'Language'          => 'Language',

		    ));
        $fields->addFieldToTab("Root.SiteBanner", new LiteralField ('SiteBanner','<h2>Site Banner</h2><p>The site banner displays at the top of all pages.</p>'));
        $fields->addFieldToTab("Root.SiteBanner", new CheckboxField ('DisplaySiteBanner','Display The Site Banner'));
        $fields->addFieldToTab("Root.SiteBanner",$settings);
    }

    public function getSiteBannerMessage(){
        $current_lang = UserLanguage::getCurrentUserLang();

        $previous_banner_rank = Session::get('bannerRank');

	    $filters = array('Language' => $current_lang);

        if (is_numeric($previous_banner_rank))  {
	        $filters['SiteBannerRank:GreaterThan'] = (int)$previous_banner_rank;
        }

	    $settings = SiteBannerConfigurationSetting::get()->filter($filters)->sort('SiteBannerRank','ASC')->first();

        // if there is no banner maybe the previous one was the last one, so we look for the first one
        if(!$settings && $previous_banner_rank)
            $settings = SiteBannerConfigurationSetting::get()->filter(array('Language' => $current_lang))->sort('SiteBannerRank','ASC')->first();

        //if there is still no banner we fetch the english one
        if(!$settings) {
	        $filters = array('Language' => 'English');
            if (is_numeric($previous_banner_rank)) {
	            $filters['SiteBannerRank:GreaterThan'] = (int)$previous_banner_rank;
            }

	        $settings = SiteBannerConfigurationSetting::get()->filter($filters)->order('SiteBannerRank','ASC')->first();

	        // if there is no banner maybe the previous one was the last one, so we look for the first one
            if(!$settings && is_numeric($previous_banner_rank))
	            $settings = SiteBannerConfigurationSetting::get()->filter(array('Language' => 'English'))->sort('SiteBannerRank','ASC')->first();

        }

        Session::set('bannerRank',$settings->SiteBannerRank);

        return $settings ?$settings->SiteBannerMessage:'';
    }

    public function getSiteBannerButtonText(){
        $current_lang = UserLanguage::getCurrentUserLang();
	    $settings = SiteBannerConfigurationSetting::get()->filter('Language', $current_lang)->first();
	    if(!$settings)
		    $settings = SiteBannerConfigurationSetting::get()->filter('Language', 'English')->first();
        return $settings?$settings->SiteBannerButtonText:'';
    }

    public function getSiteBannerButtonLink(){
        $current_lang = UserLanguage::getCurrentUserLang();
	    $settings = SiteBannerConfigurationSetting::get()->filter('Language', $current_lang)->first();
	    if(!$settings)
		    $settings = SiteBannerConfigurationSetting::get()->filter('Language', 'English')->first();
        return $settings?$settings->SiteBannerButtonLink:'';
    } 

}
