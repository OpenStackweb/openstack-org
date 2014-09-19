<?php
  
class CustomSiteConfig extends DataExtension {

	private static $db=  array(
		'DisplaySiteBanner' => 'Boolean'
	);
	private static $has_many = array('SiteBannerConfigurationSettings'=>'SiteBannerConfigurationSetting');
  
    public function updateCMSFields(FieldList $fields) {
        $settings = new GridField('SiteBannerConfigurationSettings','SiteBannerConfigurationSetting',$this->owner->SiteBannerConfigurationSettings());
        $fields->addFieldToTab("Root.SiteBanner", new LiteralField ('SiteBanner','<h2>Site Banner</h2><p>The site banner displays at the top of all pages.</p>'));
        $fields->addFieldToTab("Root.SiteBanner", new CheckboxField ('DisplaySiteBanner','Display The Site Banner'));
        $fields->addFieldToTab("Root.SiteBanner",$settings);
    }

    public function getSiteBannerMessage(){
        $current_lang = UserLanguage::getCurrentUserLang();
        $settings = SiteBannerConfigurationSetting::get()->filter('Language', $current_lang)->first();
        if(!$settings)
            $settings = SiteBannerConfigurationSetting::get()->filter('Language', 'English')->first();
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