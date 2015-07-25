<?php

class SummitAdmin extends ModelAdmin implements PermissionProvider
{
    private static $url_segment = 'summits';

    private static $managed_models = array
    (
        'Summit',
    );

    public function init() {
        parent::init();
        $res = Permission::check("ADMIN") || Permission::check("ADMIN_SUMMIT_APP") || Permission::check("ADMIN_SUMMIT_APP_SCHEDULE");
        if(!$res) Security::permissionFailure();
    }

    private static $menu_title = 'Summits';

    public function providePermissions() {
        return array(
            'ADMIN_SUMMIT_APP' => array(
                'name'     => 'Full Access to Summit App',
                'category' => 'Summit Application',
                'help'     => '',
                'sort'     => 0
            ),
            'ADMIN_SUMMIT_APP_SCHEDULE' => array(
                'name'     => 'Full Access to Summit App Schedule',
                'category' => 'Summit Application',
                'help'     => '',
                'sort'     => 1
            ),
        );
    }
}