<?php

/**
 * Copyright 2017 OpenStack Foundation
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
final class PresentationSpeakerAdminController extends Controller
{
    /**
     * @var  SummitAppAdminController The parent controller
     */
    protected $parent;

    private static $allowed_actions = array
    (
        'speakers',
        'editSpeaker',
        'speakersMerge',
    );

    private static $url_handlers = array
    (
        'merge'             => 'speakersMerge',
        '$SpeakerID!'       => 'editSpeaker',
        'GET '              => 'speakers',
    );

    /**
     * PresentationSpeakerAdminController constructor.
     * @param SummitAppAdminController $parent
     */
    public function __construct(SummitAppAdminController $parent)
    {
        parent::__construct();
        $this->parent       = $parent;
    }

    public function Link($action = null)
    {
        return $this->parent->Link($action);
    }

    public function speakers(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-addspeaker.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summitapp-addspeaker.js');

        return $this->parent->getViewer('speakers')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                        )
                    )
            );
    }

    public function speakersMerge(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summit-admin-speaker-merge.css');

        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('themes/openstack/javascript/bootstrap-paginator/src/bootstrap-paginator.js');
        Requirements::javascript('themes/openstack/javascript/urlfragment.jquery.js');
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
        Requirements::javascript('summit/javascript/summit-admin-speaker-merge.js');

        return $this->parent->getViewer('speakers_merge')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                        )
                    )
            );
    }

    public function editSpeaker(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $speaker_id = intval($request->param('SpeakerID'));
        $speaker    = PresentationSpeaker::get()->byID($speaker_id);

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-speaker.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        // tag input
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::javascript('themes/openstack/bower_assets/sweetalert/dist/sweetalert.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/jquery.validate.min.js');
        Requirements::javascript('themes/openstack/bower_assets/jquery-validate/dist/additional-methods.min.js');
        Requirements::javascript('themes/openstack/bower_assets/chosen/chosen.jquery.min.js');
        Requirements::javascript('summit/javascript/simple-sidebar.js');
        Requirements::javascript('//tinymce.cachefly.net/4.3/tinymce.min.js');
        Requirements::javascript('themes/openstack/bower_assets/typeahead.js/dist/typeahead.bundle.min.js');
        Requirements::javascript('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js');
        Requirements::javascript('summit/javascript/summitapp-editspeaker.js');

        return $this->parent->getViewer('EditSpeaker')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'   => $summit,
                            'Speaker' => $speaker,
                        )
                    )
            );
    }

}