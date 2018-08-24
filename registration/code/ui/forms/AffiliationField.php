<?php

/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
class AffiliationField extends FormField
{
    /**
     * @var string
     */
    private $mode = 'remote';

    /**
     * @param string $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function FieldHolder($attributes = array ()) {
        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        Requirements::css("registration/css/affiliations.css");
        JSChosenDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements();

        $js_scripts = array(
            "node_modules/pure/libs/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            'registration/javascript/affiliations.js',
        );

        foreach($js_scripts as $js)
            Requirements::javascript($js);

        $arrayData = new ArrayData(array(
            'Title' => 'Edit Affiliation',
        ));

        $modal  = $arrayData->renderWith('AffiliationModalForm');
        $modal  = trim(preg_replace('/\s\s+/', ' ', $modal));
        $script = <<<JS

        (function( $ ){

            $(document).ready(function() {
                $('{$modal}').appendTo($('body'));
                $("#edit-affiliation-form").affiliations({
                    storage:'{$this->mode}'
                });
            });


        }( jQuery ));
JS;

        Requirements::customScript($script);

        return parent::FieldHolder($attributes);
    }

}