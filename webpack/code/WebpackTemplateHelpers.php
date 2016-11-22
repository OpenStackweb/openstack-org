<?php

class WebpackTemplateHelpers implements TemplateGlobalProvider
{

    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'ModuleJS',
            'ModuleCSS',
            'WebpackDevServer'
        ];
    }

    /** 
     * Given the current controller, determine the module name.
     * If the template and controller are in different modules (e.g. themes/), 
     * this probably won't produce the expected result on a template.
     *
     * @return  string
     */
    public static function ModuleName()
    {
    	$class = Controller::curr()->class;
    	$file = SS_ClassLoader::instance()->getItemPath($class);

    	if($file) {
    		$dir = dirname($file);
    		while(dirname($dir) != BASE_PATH) {
    			$dir = dirname($dir);
    		}

    		return basename($dir);
    	}

    	return null;
    }

    /**
     * Returns true if the Webpack dev server is running
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

    /**
     * Include a JS module bundle. If a dev server is running, use it.
     *
     * Example: $ModuleJS('main') -> /<your-current-module>/ui/production/js/main.js
     * 
     * @param string $filename [description]
     * @return  DBField
     */
    public static function ModuleJS($filename, $withRequirements = false)
    {   
    	$prodPath = self::WebpackDevServer() ?
    		Config::inst()->get('Webpack','dev_server_baseurl') :
    		Controller::join_links(self::ModuleName(), 'ui/production');

    	$link = Controller::join_links(
			$prodPath, 
			'js',
			self::with_extension($filename, 'js')
		);

    	if(!Director::is_absolute_url($link)){
    		$link = Director::absoluteURL($link);
    	}

    	if($withRequirements) {
    		return Requirements::javascript($link);
    	}

		return DBField::create_field('HTMLText', sprintf(
			'<script type="text/javascript" src="%s"></script>',
			$link
		));    	
    }

    /**
     * Include a CSS module bundle. If dev server is running, skip it, as CSS
     * is included with JS.
     *
     * Example: $ModuleCSS('main') -> /<your-current-module>/ui/production/css/main.css
     * 
     * @param string $filename [description]
     */
    public static function ModuleCSS($filename, $withRequirements = false)
    {        
    	if(!self::WebpackDevServer()) {
			$link = Controller::join_links(
				Director::absoluteBaseURL(),
				self::ModuleName(),
				'ui/production/css',
				self::with_extension($filename, 'css')
			);

			if($withRequirements) {		
				return Requirements::css($link);
			}

			return DBField::create_field('HTMLText', sprintf(
				'<link rel="stylesheet" type="text/css" href="%s" />',
				$link
			));
    	}
    }

    /**
     * Ensures a file has a given extension. Will not "fix" an extension.
     * Only adds when missing.
     * 	
     * @param  string $file 
     * @param  string $ext
     * @return string
     */
    private static function with_extension($file, $ext)
    {
    	$info = pathinfo($file);
    	if(empty($info['extension'])) {
    		return "{$file}.{$ext}";
    	}

    	return $file;
    }
}
