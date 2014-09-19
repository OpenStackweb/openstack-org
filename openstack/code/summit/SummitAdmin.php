<?php
class SummitAdmin extends ModelAdmin {
 
    public static $managed_models = array(
        'Summit',
        'SummitCategory'
    );
 
    static $url_segment = 'summits';
    static $menu_title = 'Summits';
}