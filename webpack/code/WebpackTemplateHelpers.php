<?php

class WebpackTemplateHelpers implements TemplateGlobalProvider
{
    /**
     * @return array
     */
    public static function get_template_global_variables()
    {
        return [
            'ModuleName' => [
              'method' => 'module_name',
              'casting' => 'Text',
            ],
            'ModuleJS' => [
              'method' => 'module_js',
              'casting' => 'HTMLText',
            ],
            'ModuleCSS' => [
              'method' => 'module_css',
              'casting' => 'HTMLText',
            ],
            'RenderComponent' => [
              'method' => 'render_component',
              'casting' => 'HTMLText',
            ],
            'WebpackDevServer' => [
              'method' => 'is_webpack',
              'casting' => 'Boolean',
            ],
        ];
    }

    /**
     * Given the current controller, determine the module name.
     * If the template and controller are in different modules (e.g. themes/),
     * this probably won't produce the expected result on a template.
     *
     * @return string
     */
    public static function module_name()
    {
        $class = Controller::curr()->class;
        $file = SS_ClassLoader::instance()->getItemPath($class);

        if ($file) {
            $dir = dirname($file);
            while (dirname($dir) != BASE_PATH) {
                $dir = dirname($dir);
            }

            $module = basename($dir);
            if ($module == 'openstack') {
                $module = Injector::inst()->get('ViewableData')->ThemeDir();
            }

            return $module;
        }

        return null;
    }

    /**
     * Returns true if the Webpack dev server is running.
     *
     * @return bool
     */
    public static function is_webpack()
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
     *
     * @return DBField
     */
    public static function module_js($filename, $withRequirements = false, $module_name = null)
    {
        $withRequirements = filter_var($withRequirements, FILTER_VALIDATE_BOOLEAN);
        $hasCommon  = false;
        $commonLink = null;
        $prodLink   = null;
        if(empty($module_name)) $module_name = self::module_name();

        if (self::is_webpack()) {
            $devURL = Config::inst()->get('Webpack', 'dev_server_baseurl');
            $commonLink = Controller::join_links(
                $devURL,
                'js',
                '__common__.js'
            );
            $prodLink = Controller::join_links(
                $devURL,
                'js',
                self::with_extension($filename, 'js')
            );

            $headers   = get_headers($commonLink, 1);
            $hasCommon = preg_match('/200/', $headers[0]);
        } else {
            $prodLink = Controller::join_links(
                $module_name,
                'ui/production/js',
                self::with_extension($filename, 'js')
            );

            $commonLink = Controller::join_links(
                $module_name,
                'ui/production/js/__common__.js'
            );

            $hasCommon  = Director::fileExists($commonLink);
            $commonLink = Director::absoluteURL($commonLink);
            $prodLink   = Director::absoluteURL($prodLink);
        }

        if ($withRequirements) {
            if ($hasCommon) {
                Requirements::javascript($commonLink);
            }
            Requirements::javascript($prodLink);

            return;
        }

        $tags = [];
        $tag = '<script type="text/javascript" src="%s"></script>';

        if ($hasCommon) {
            $tags[] = sprintf($tag, $commonLink);
        }
        $tags[] = sprintf($tag, $prodLink);

        return implode(PHP_EOL, $tags);
    }

    /**
     * Include a CSS module bundle. If dev server is running, skip it, as CSS
     * is included with JS.
     *
     * Example: $ModuleCSS('main') -> /<your-current-module>/ui/production/css/main.css
     *
     * @param string $filename [description]
     */
    public static function module_css($filename, $withRequirements = true,  $module_name = null)
    {
        $hasCommon  = false;
        $commonLink = null;
        $withRequirements = filter_var($withRequirements, FILTER_VALIDATE_BOOLEAN);
        if(empty($module_name)) $module_name = self::module_name();

        // If webpack dev server is running, the CSS is injected with JS
        if (self::is_webpack()) {
            return;
        }

        $link = Controller::join_links(
            Director::absoluteBaseURL(),
            $module_name,
            'ui/production/css',
            self::with_extension($filename, 'css')
        );

        $commonLink = Controller::join_links(
            $module_name,
            'ui/production/css/common.css'
        );

        $hasCommon  = Director::fileExists($commonLink);
        $commonLink = Director::absoluteURL($commonLink);

        if ($withRequirements) {
            if ($hasCommon) {
                Requirements::css($commonLink);
            }
            return Requirements::css($link);
        }

        $tags = [];
        $tag  ='<link rel="stylesheet" type="text/css" href="%s" />';

        if ($hasCommon) {
            $tags[] = sprintf($tag, $commonLink);
        }
        $tags[] = sprintf($tag, $link);

        return implode(PHP_EOL, $tags);
    }

    /**
     * Renders a global React component
     * Loads the requisite autoload JS to mount the component.
     *
     * @param string $name The name of the component
     *
     * @return string
     */
    public static function render_component($name)
    {
        self::module_js('automount', true);
        self::module_js($name, true);

        return sprintf(
            '<div data-component="%s"></div>',
            $name
        );
    }

    /**
     * Ensures a file has a given extension. Will not "fix" an extension.
     * Only adds when missing.
     *
     * @param string $file
     * @param string $ext
     *
     * @return string
     */
    private static function with_extension($file, $ext)
    {
        $info = pathinfo($file);
        if (empty($info['extension'])) {
            return "{$file}.{$ext}";
        }

        return $file;
    }
}
