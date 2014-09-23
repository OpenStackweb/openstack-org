<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 8/29/13
 * Time: 3:02 PM
 * To change this template use File | Settings | File Templates.
 */

class SiteBannerConfigurationSetting extends DataObject {

    public static $db = array(
        'SiteBannerMessage' => 'HTMLText',
        'SiteBannerButtonText' => 'Text',
        'SiteBannerButtonLink' => 'Text',
        'SiteBannerRank' => 'Int',
        'Language'=> "Enum('English, Spanish, Italian, German, Portuguese,Chinese,Japanese,French', 'English')",
    );

    public static $has_one=Array('SiteConfig'=>'SiteConfig');

    function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('SiteConfigID');
        return $fields;
    }
}