<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 7/17/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

class CompanyAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'Company'
    );

    public $showImportForm = false;
    static $url_segment = 'companies';
    static $menu_title = 'Companies';

    public function init()
    {
        parent::init();
        Requirements::css(THEMES_DIR . "/openstack/css/company_admin.css");
    }
}