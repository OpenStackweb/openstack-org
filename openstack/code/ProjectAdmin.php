<?php
class ProjectAdmin extends ModelAdmin {

	private static $managed_models = array(
        'Project'
    );
 
    static $url_segment = 'projects';
    static $menu_title = 'Projects';
}