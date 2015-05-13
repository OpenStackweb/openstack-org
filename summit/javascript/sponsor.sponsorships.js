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

(function( $ ){

    var errors = {
        package_not_run_out : 'We sorry, package is not available anymore.',
        package_already_bought: 'You already bought that package.',
        generic: 'There was an error with your request. please try it again.'
    };

    var packages_ordered = [];
    var jqxhr_packages = null;
    var packages_pull_interval = 20000;//20s
    var package_html_template = $('<div class="sponsor_package col-lg-4 col-md-4 col-sm-4"><div class="sponsor-spots"><h3 class="package-title"><span class="package-sub-title"></span></h3><div class="sponsor-cost"></div><div class="sponsor-count"></div></div><div class="package-actions"><button type="button" class="btn btn-primary buy-package">Buy Me</button></div></div>');
    var package_directives = {
        '.package-title' : 'title',
        '.sponsor-cost'  : 'cost',
        '.sponsor-spots@class' : function(a){
            var item = a.context;
            return item.available === 0? 'sponsor-spots sold-out':'sponsor-spots';
        },
        '.sponsor-count' : function(a){
            var item = a.context;
            if(item.available === 0) return 'Sold Out';
            if(item.show_availability){
                return '<td>'+item.available+' of '+item.max_available+'</td>';
            }
            else{
                return '<td>Still Available</td>';
            }
        },
        ".buy-package@data-id":'id',
        ".buy-package@data-available":'available',
        ".buy-package@data-title":'title'
    };

    var jqxhr_add_ons = null;
    var add_ons_pull_interval = 20000;//20s
    var add_on_html_template = $('<tr class="sponsor_add_on"><td class="add_on_title"></td><td class="add_on_cost"></td><td class="add_on_status"></td></tr>');
    var add_on_directives = {
        '.add_on_title' : 'title',
        '.add_on_cost' : 'cost',
        '.add_on_status':function(a){
            var item = a.context;
            if(item.available === 0) return 'Sold Out';
            if(item.show_availability){
                return item.available+' of '+item.max_available;
            }
            else{
                return 'Still Available';
            }
        }
    };

    var current_package_id = null;

    function buySponsorPackage(evt){

        current_package_id = parseInt($(this).attr('data-id'));
        var available      = parseInt($(this).attr('data-available'));
        var title          = $(this).attr('data-title');
        var already_bought = $.inArray(current_package_id, packages_ordered) >= 0;

        if(available <= 0) {
            alert(errors.package_not_run_out);
            return false;
        }

        if(already_bought){
            alert(errors.package_already_bought);
            return false;
        }

        $('.modal-title', '#summit_package_purchase_order_modal').text('Sponsorship Package '+title+' - Purchase Order');
        form_validator.resetForm();
        $('#summit_package_purchase_order_modal').modal('show');
    }

    function performBuySponsorPackage(evt){

        if(current_package_id !== null && current_package_id > 0){

            var available  = parseInt($('#package_'+current_package_id).attr('data-available'));

            if(available > 0){

                var is_valid = form.valid();

                if(is_valid){

                    $('#summit_package_purchase_order_modal').modal('hide');

                    var purchase_order = {
                        package_id      : current_package_id,
                        first_name      : $('#summit_package_purchase_order_fname', form).val(),
                        last_name       : $('#summit_package_purchase_order_lname', form).val(),
                        email           : $('#summit_package_purchase_order_email', form).val(),
                        organization    : $('#summit_package_purchase_order_org', form).val(),
                        organization_id : parseInt($('#summit_package_purchase_order_org_id', form).val()),
                        summit_page_id  : page_id
                    }

                    var token = $('#packagePurchaseOrderSecurityToken', form).val();

                    $.ajax({
                        type: 'POST',
                        url: urls.emitPackagePurchaseOrder +'?packagePurchaseOrderSecurityToken='+token,
                        data: JSON.stringify(purchase_order),
                        contentType: "application/json; charset=utf-8",
                        success: function (data,textStatus,jqXHR) {
                            packages_ordered.push( parseInt(purchase_order.package_id));
                            if(data === null) return;
                            //update security token for next purchase
                            $('#packagePurchaseOrderSecurityToken', form).val(data.token);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert(errors.generic);
                        }
                    });

                    current_package_id = null;
                    form_validator.resetForm();
                    form.cleanForm();
                }
            }
            else{
                $('#summit_package_purchase_order_modal').modal('hide')
                alert(errors.package_not_run_out);
            }
        }

    }

    var form = null;
    var form_validator = null;

    $(document).ready(function(){

        // override jquery validate plugin defaults
        $.validator.setDefaults({
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                if(element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });

        form  = $('#summit_package_purchase_order_form');
        form_validator = form.validate({
            rules: {
                'summit_package_purchase_order_fname ': {required: true },
                'summit_package_purchase_order_lname' : {required: true },
                'summit_package_purchase_order_email' : {required: true, email:true},
                'summit_package_purchase_order_org'   : {required: true },
                'summit_package_purchase_order_terms' : {required: true }
            }
        });

        $('#summit_package_purchase_order_modal').modal({
            show:false
        })

        $('#summit_package_purchase_order_buy_btn').click(performBuySponsorPackage);

        var input = $('#summit_package_purchase_order_org', form);

        input.autocomplete({
            source: urls.searchOrg,
            minLength: 2,
            appendTo: "#summit_package_purchase_order_form",
            select: function( event, ui ) {
                $('#summit_package_purchase_order_org_id', form).val(ui.item ? ui.item.id : 0);
            }
        });

        $('.buy-package').live('click', buySponsorPackage);

        setInterval(function(){
            if(jqxhr_packages === null) {
                jqxhr_packages = $.get("/api/v1/summits/"+page_id+"/packages", function (data) {

                    if(data === null || !$.isArray(data) || data.length == 0) return;

                    var container = $('#packages_container');


                    $('.buy-package').die();

                    container.empty();

                    data.forEach(function (summit_package)
                    {
                        var summit_package_html = package_html_template.render(summit_package, package_directives);
                        var buy_button          = $('.buy-package', summit_package_html);

                        buy_button.attr('id',"package_"+summit_package.id);
                        if(summit_package.available > 0){
                            summit_package_html.attr('title',"Buy me");
                            buy_button.show();
                        }
                        else{
                            buy_button.hide();
                        }
                        container.append(summit_package_html);
                    });

                    $('.buy-package').live('click', buySponsorPackage);

                }).fail(function () {

                }).always(function() {
                    jqxhr_packages = null;
                });
            }
        }, packages_pull_interval);

        setInterval(function(){
            if(jqxhr_add_ons === null) {
                jqxhr_add_ons = $.get("/api/v1/summits/"+page_id+"/add-ons", function (data) {

                    if(data === null || !$.isArray(data) || data.length == 0) return;

                    var container = $('#add_ons');

                    $( ".sponsor_add_on" ).die();

                    container.empty();

                    data.forEach(function (summit_add_on)
                    {
                        var summit_add_on_html = add_on_html_template.render(summit_add_on, add_on_directives);
                        summit_add_on_html.attr('id',"add_on_"+summit_add_on.id);
                        if(summit_add_on.available === 0)
                            summit_add_on_html.addClass('sold-out')
                        container.append(summit_add_on_html);
                    });

                }).fail(function () {

                }).always(function() {
                    jqxhr_add_ons = null;
                });
            }
        }, add_ons_pull_interval);

    });

    // End of closure.
}( jQuery ));