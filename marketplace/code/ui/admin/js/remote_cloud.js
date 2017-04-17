/**
 * Copyright 2014 Openstack Foundation
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
jQuery(document).ready(function($){

    var form = $("#remote_cloud_form");

    if(form.length > 0){

        form.marketplace_type_header();
        form.implementation_openstack_powered();

        $("#components_form").components();
        $("#hypervisors_form").hypervisors();
        $("#guest_os_form").guest_os();
        $("#videos-form").videos();
        $("#support-channels-form").support_channels();
        $("#additional-resources-form").additional_resources();

        //if we are editing data, load it ...
        if(typeof(remote_cloud)!=='undefined'){
            //populate form and widgets
            $("#company_id",form).val(remote_cloud.company_id);
            $("#company_id").trigger("chosen:updated");
            $("#name",form).val(remote_cloud.name);
            $("#overview",form).val(remote_cloud.overview);
            $("#call_2_action_uri",form).val(remote_cloud.call_2_action_uri);
            $("#hardware_specifications",form).val(remote_cloud.hardware_specifications);
            $("#pricing_models",form).val(remote_cloud.pricing_models);
            $("#published_slas",form).val(remote_cloud.published_slas);

            if(remote_cloud.vendor_managed_upgrades){
                $('#vendor_managed_upgrades',form).prop('checked',true);
            }
            else{
                $('#vendor_managed_upgrades',form).prop('checked',false);
            }

            if(remote_cloud.active){
                $('#active',form).prop('checked',true);
            }
            else{
                $('#active',form).prop('checked',false);
            }

            //this is a draft
            if (typeof(remote_cloud.live_service_id) != 'undefined') {
                $("#id",form).val(remote_cloud.id);
                $("#live_id",form).val(remote_cloud.live_service_id);
            } else { //its not a draft is the live version, so we remove the id and set the live_service_id
                $("#live_id",form).val(remote_cloud.id);
                $('.publish-remote-cloud').prop('disabled',true);
            }
            form.implementation_openstack_powered('load', remote_cloud);
            //reload widgets
            $("#components_form").components('load',remote_cloud.capabilities);
            $("#hypervisors_form").hypervisors('load',remote_cloud.hypervisors);
            $("#guest_os_form").guest_os('load',remote_cloud.guest_os);
            $("#videos-form").videos('load',remote_cloud.videos);
            $("#support-channels-form").support_channels('load',remote_cloud.regional_support);
            $("#additional-resources-form").additional_resources('load',remote_cloud.additional_resources);
        }

        $('.save-remote-cloud').click(function(event){

            tinyMCE.triggerSave();
            event.preventDefault();
            event.stopPropagation();
            var button =  $(this);
            if(button.prop('disabled')){
                return false;
            }

            var form_validator = form.marketplace_type_header('getFormValidator');
            form_validator.settings.ignore = ".add-comtrol";
            var is_valid = form.valid();
            if(!is_valid) return false;
            form_validator.resetForm();

            var remote_cloud = serializeRemoteCloud(form, false);

            if(remote_cloud !== false) {
                ajaxIndicatorStart('saving data.. please wait..');

                var type = remote_cloud.id > 0 ?'PUT':'POST';

                $('.save-remote-cloud').prop('disabled',true);
                $.ajax({
                    type: type,
                    url: 'api/v1/marketplace/remote-clouds',
                    data: JSON.stringify(remote_cloud),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        ajaxIndicatorStop();
                        $('.publish-remote-cloud').prop('disabled',false);
                        $('.save-remote-cloud').prop('disabled',false);
                        window.location = listing_url;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxIndicatorStop();
                        $('.save-remote-cloud').prop('disabled',false);
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
            return false;
        });

        $('.preview-remote-cloud').click(function(event){
            tinyMCE.triggerSave();
            event.preventDefault();
            event.stopPropagation();
            var button =  $(this);
            if(button.prop('disabled')){
                return false;
            }
            var form_validator = form.marketplace_type_header('getFormValidator');
            form_validator.settings.ignore = ".add-comtrol";
            var is_valid = form.valid();
            if(!is_valid) return false;
            form_validator.resetForm();

            var is_pdf      = $(this).hasClass('pdf');
            var remote_cloud = serializeRemoteCloud(form, false);

            if(remote_cloud !== false) {

                ajaxIndicatorStart('saving data.. please wait..');

                var type = remote_cloud.id > 0 ?'PUT':'POST';

                $('.save-remote-cloud').prop('disabled',true);

                $.ajax({
                    type: type,
                    url: 'api/v1/marketplace/remote-clouds',
                    data: JSON.stringify(remote_cloud),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        ajaxIndicatorStop();
                        $('.publish-remote-cloud').prop('disabled',false);
                        $('.save-remote-cloud').prop('disabled',false);
                        var draft_id = (remote_cloud.id > 0) ? remote_cloud.id : data;
                        $("#id",form).val(draft_id);

                        if (is_pdf) {
                            window.location = product_url+'/'+draft_id+'/draft_pdf';
                        } else {
                            window.open(product_url+'/'+draft_id+'/draft_preview','_blank');
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxIndicatorStop();
                        $('.save-remote-cloud').prop('disabled',false);
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            }

            return false;
        });

        $('.publish-remote-cloud').click(function(event){
            tinyMCE.triggerSave();
            event.preventDefault();
            event.stopPropagation();
            var button =  $(this);
            if(button.prop('disabled')){
                return false;
            }
            var form_validator = form.marketplace_type_header('getFormValidator');
            form_validator.settings.ignore = ".add-comtrol";
            var is_valid = form.valid();
            if(!is_valid) return false;
            form_validator.resetForm();

            var remote_cloud = serializeRemoteCloud(form, true);

            if(remote_cloud !== false){

                ajaxIndicatorStart('saving data.. please wait..');

                var url  = 'api/v1/marketplace/remote-clouds/'+remote_cloud.live_service_id;

                $('.publish-remote-cloud').prop('disabled',true);

                $.ajax({
                    type: 'PUT',
                    url: url,
                    data: JSON.stringify(remote_cloud),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        ajaxIndicatorStop();
                        window.location = listing_url;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        ajaxIndicatorStop();
                        $('.publish-remote-cloud').prop('disabled',false);
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
            return false;

        });
    }
});

/**
 *
 * @param form
 * @param publish
 */
function serializeRemoteCloud(form, publish){

    var additional_resources = $("#additional-resources-form").additional_resources('serialize');
    var regional_support     = $("#support-channels-form").support_channels('serialize');
    var capabilities         = $("#components_form").components('serialize');
    var guest_os             = $("#guest_os_form").guest_os('serialize');
    var hypervisors          = $("#hypervisors_form").hypervisors('serialize');
    var videos               = $("#videos-form").videos('serialize');

    if(additional_resources !== false &&
        regional_support    !== false &&
        capabilities        !== false &&
        guest_os            !== false &&
        hypervisors         !== false &&
        videos              !== false){

        var remote_cloud = {};

        remote_cloud.id                      = parseInt($("#id",form).val());
        remote_cloud.live_service_id         = parseInt($("#live_id",form).val());
        remote_cloud.company_id              = parseInt($("#company_id",form).val());
        remote_cloud.name                    = $("#name",form).val();
        remote_cloud.overview                = $("#overview",form).val();
        remote_cloud.call_2_action_uri       = $("#call_2_action_uri",form).val();
        remote_cloud.hardware_specifications = $("#hardware_specifications",form).val();
        remote_cloud.pricing_models          = $("#pricing_models",form).val();
        remote_cloud.published_slas          = $("#published_slas",form).val();
        remote_cloud.vendor_managed_upgrades = $('#vendor_managed_upgrades',form).is(":checked");
        remote_cloud.active                  = $('#active',form).is(":checked");
        remote_cloud.videos                  = videos;
        remote_cloud.hypervisors             = hypervisors;
        remote_cloud.guest_os                = guest_os;
        remote_cloud.capabilities            = capabilities;
        remote_cloud.regional_support        = regional_support;
        remote_cloud.additional_resources    = additional_resources;
        remote_cloud                         = form.implementation_openstack_powered('serialize', remote_cloud);
        remote_cloud.published               = publish? 1:0;

        return remote_cloud;
    }

    return false;
}