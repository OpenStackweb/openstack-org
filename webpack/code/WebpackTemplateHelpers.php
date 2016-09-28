<?php

class WebpackTemplateHelpers implements TemplateGlobalProvider
{

    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'WebpackJS',
            'WebpackCSS',
            'WebpackDevServer',
            'LoadComponent',
            'RenderComponent'
        ];
    }

    /**
     * @return bool
     */
    public static function WebpackDevServer()
    {
        $baseURL = Config::inst()->get('Webpack', 'dev_server_baseurl');
        $parts = parse_url($baseURL);
        $port = $parts['port'];

        if (Director::isDev()) {
            $socket = @fsockopen('localhost', $port, $errno, $errstr, 1);
            return !$socket ? false : true;
        }
    }


    public static function WebpackJS($prodPath, $devFile)
    {        
    	if(self::WebpackDevServer()) {
    		$prodPath = Config::inst()->get('Webpack','dev_server_baseurl');    		
    	}

		return DBField::create_field('HTMLText', sprintf(
			'<script type="text/javascript" src="%s"></script>',
			Controller::join_links($prodPath, $devFile)
		));    	
    }


    public static function WebpackCSS($filename)
    {        
    	if(!self::WebpackDevServer()) {
			return DBField::create_field('HTMLText', sprintf(
				'<link rel="stylesheet" type="text/css" href="%s" />',
				$filename
			));
    	}
    }


    public static function LoadComponent($component, $module = null)
    {
    	if(!$module) {
    		$module = Injector::inst()->get('ViewableData')->ThemeDir();
    	}

    	Requirements::javascript($module.'/ui/production/commons.chunk.js');
    	Requirements::javascript($module.'/ui/production/js/'.$component.'.bundle.js');
    }


    public static function RenderComponent($component, $module = null)
    {
    	self::LoadComponent($component, $module);

    	$ret = DBField::create_field(
    		'HTMLText', 
    		sprintf('<div data-component="%s"></div>', $component)
    	);

    	return $ret;
    }
}
