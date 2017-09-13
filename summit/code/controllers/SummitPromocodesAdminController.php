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
final class SummitPromocodesAdminController extends Controller
{
    /**
     * @var  SummitAppAdminController The parent controller
     */
    protected $parent;

    /**
     * @var ISummitRegistrationPromoCodeRepository
     */
    private $promocode_repository;

    private static $allowed_actions = array
    (
        'promocodes',
        'editPromoCode',
        'editPromoCodeSponsor',
        'promocodesSponsors',
        'promocodesBulk',
    );

    private static $url_handlers = array
    (
        'sponsors/$SponsorID!'      => 'editPromoCodeSponsor',
        'sponsors'                  => 'promocodesSponsors',
        'bulk'                      => 'promocodesBulk',
        '$Code!'                    => 'editPromoCode',
        'GET '                      => 'promocodes',
    );

    /**
     * SummitPromocodesAdminController constructor.
     * @param SummitAppAdminController $parent
     */
    public function __construct(SummitAppAdminController $parent)
    {
        parent::__construct();
        $this->parent       = $parent;
        $this->promocode_repository = new SapphireSummitRegistrationPromoCodeRepository();
    }

    public function Link($action = null)
    {
        return $this->parent->Link($action);
    }

    public function promocodes(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));

        $summit = Summit::get()->byID($summit_id);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-promocode.css');

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
        Requirements::javascript('summit/javascript/summitapp-promocode.js');

        return $this->parent->getViewer('promocodes')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'CodeTypes' => $promocode_types,
                        )
                    )
            );
    }

    public function editPromoCode(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $code       = $request->param('Code');
        $promo_code = $this->promocode_repository->getByCode($summit_id, $code);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-promocode.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
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
        Requirements::javascript('summit/javascript/summitapp-editpromocode.js');

        return $this->parent->getViewer('editPromoCode')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'         => $summit,
                            'PromoCode'      => $promo_code,
                            'PromoCodeTypes' => $promocode_types,
                        )
                    )
            );
    }

    public function promocodesSponsors(SS_HTTPRequest $request)
    {
        $summit_id = intval($request->param('SummitID'));
        $summit = Summit::get()->byID($summit_id);
        $promocodes = $this->promocode_repository->getGroupedBySponsor($summit_id);

        Requirements::css('summit/css/simple-sidebar.css');
        // tag inputes
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput.css');
        Requirements::css('themes/openstack/bower_assets/bootstrap-tagsinput/dist/bootstrap-tagsinput-typeahead.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('summit/css/summitapp-promocode.css');

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
        Requirements::javascript('summit/javascript/summitapp-promocode.js');

        return $this->parent->getViewer('promocodes_sponsors')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit' => $summit,
                            'PromoCodes' => $promocodes,
                        )
                    )
            );
    }

    public function editPromoCodeSponsor(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $sponsor_id = $request->param('SponsorID');
        $sponsor    = (is_numeric($sponsor_id)) ? Company::get_by_id('Company',$sponsor_id) : null;
        $promocodes = (is_numeric($sponsor_id)) ? $this->promocode_repository->getBySponsor($summit_id, $sponsor_id) : array();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-edit-promocode.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
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
        Requirements::javascript('summit/javascript/summitapp-editpromocode-sponsor.js');

        return $this->parent->getViewer('editPromoCodeSponsor')->process
            (
                $this->customise
                    (
                        array
                        (
                            'Summit'         => $summit,
                            'PromoCodes'     => $promocodes,
                            'Sponsor'        => $sponsor,
                        )
                    )
            );
    }

    public function promocodesBulk(SS_HTTPRequest $request)
    {
        $summit_id  = intval($request->param('SummitID'));
        $summit     = Summit::get()->byID($summit_id);
        $promocode_types = SummitRegistrationPromoCode::getTypes();

        Requirements::css('summit/css/simple-sidebar.css');
        Requirements::css('summit/css/summit-admin-promocodes-bulk.css');
        Requirements::css('themes/openstack/bower_assets/chosen/chosen.min.css');
        Requirements::css('themes/openstack/bower_assets/sweetalert/dist/sweetalert.css');
        Requirements::css('themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
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
        Requirements::javascript('summit/javascript/summitapp-promocodes-bulk.js');

        return $this->parent->getViewer('promocodes_bulk')->process
            (
                $this->customise(array(
                    'Summit' => $summit,
                    'CodeTypes' => $promocode_types
                ))
            );
    }

}