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

(function( $ ){


    var rest_urls = {
        SaveAffiliation:"/userprofile/SaveAffiliation",
        DeleteAffiliation:"/userprofile/DeleteAffiliation",
        GetAffiliation:"/userprofile/GetAffiliation",
        ListAffiliations:"/userprofile/ListAffiliations",
        ListOrganizations:"/userprofile/ListOrganizations",
        AffiliationsCount:"/userprofile/AffiliationsCount"
    };

    var settings = {};
    var local_storage = {};
    var last_id = 0;
    var affiliation_form = null;
    var affiliation_form_id ='';

    var methods = {
        init : function(options) {
            settings = $.extend({
                // These are the defaults.
                storage: "remote"//could be also "local"
            }, options );

            affiliation_form   = $(this);
            affiliation_form_id = "#"+affiliation_form.attr("id");

            if(affiliation_form.length > 0){

                var affiliation_form_validator = affiliation_form.validate({
                    rules: {
                        OrgName  : { required: true, ValidPlainText:true},
                        StartDate: { required: true, dpDate: true},
                        EndDate  : { dpDate: true, dpCompareDate:'after #StartDate' }
                    },
                    onfocusout: false,
                    invalidHandler: function(form, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            validator.errorList[0].element.focus();
                        }
                    }
                });

                // init modal form

                $('#modal-edit-affiliation').modal({
                    show:false
                });

                $('#modal-edit-affiliation').on('hidden.bs.modal', function (e) {
                    affiliation_form.cleanForm();
                    affiliation_form_validator.resetForm();
                })

                // set modal controls
                var org_name = $('#OrgName', affiliation_form);

                if(org_name.length>0){
                    org_name.autocomplete({
                        source: rest_urls.ListOrganizations,
                        minLength: 2,
                        open: function( event, ui ) {
                            org_name.autocomplete("widget").css('z-index',5000);
                        }
                    });

                }

                var date_picker_start = $('#StartDate', affiliation_form);
                date_picker_start.datepicker({dateFormat: 'yy-mm-dd'});
                var date_picker_end = $('#EndDate', affiliation_form);

                date_picker_end.datepicker({dateFormat: 'yy-mm-dd',onSelect:function(date_str,inst){
                    var date_arr = date_str.split("-");
                    var end_date = new Date(parseInt(date_arr[0]),parseInt(date_arr[1])-1,parseInt(date_arr[2]));
                    var today = new Date();
                    var current = $('#Current', affiliation_form);
                    if(end_date < today){
                        //reset checkbox
                        $('#Current', affiliation_form).prop('checked', false);
                        current.hide();
                    }
                    else
                        current.show();
                }});

                // add handler
                $("#add-affiliation").click(function(event){
                    event.preventDefault();
                    event.stopPropagation();
                    var dlg = $('#affiliation-edition-dialog');
                    var current = $('#Current',affiliation_form);
                    $('#modal-edit-affiliation').modal('show')
                    $('#editAffiliationLabel').text('Add Affiliation');
                    return false;
                });

                $('#btn-save-affiliation').click(function(event){
                    event.preventDefault();
                    event.stopPropagation();


                    var is_valid = affiliation_form.valid();
                    if (!is_valid) return;
                    var affiliation     = affiliation_form.serializeForm();
                    var checked         = $('#Current' ,affiliation_form).is(':checked');
                    affiliation.Current = checked?1:0;
                    var today           = new Date();
                    var yyyy            = today.getFullYear().toString();
                    var mm              = (today.getMonth()+1).toString(); // getMonth() is zero-based
                    var dd              = today.getDate().toString();

                    affiliation.ClientToday = yyyy +'-'+ (mm[1]?mm:"0"+mm[0])  +'-'+ (dd[1]?dd:"0"+dd[0]);
                    var $this = this;
                    switch(settings.storage){
                        case 'local':
                        {
                            if(affiliation.Id==0){
                                affiliation.Id = ++last_id;
                            }
                            local_storage[affiliation.Id] = affiliation;
                            LoadAffiliationList();
                        }
                        break;
                        default:
                        {
                            $.ajax(
                                {
                                    type: "POST",
                                    url: rest_urls.SaveAffiliation,
                                    data: JSON.stringify(affiliation),
                                    contentType: "application/json; charset=utf-8",
                                    dataType: "json",
                                    timeout:60000,
                                    retryMax: 2,
                                    complete: function (jqXHR,textStatus) {
                                        LoadAffiliationList();
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        alert( "Request failed: " + textStatus );
                                    }
                                }
                            );
                        }
                        break;
                    }
                    $('#modal-edit-affiliation').modal('hide');
                    $("#HiddenAffiliations").trigger( "affiliation:saved");
                    return false;
                });

                // delete handler

                $(".del-affiliation").live('click',function(event){
                    var id = $(this).attr('data-id');
                    if(window.confirm("Are you sure?")){
                        switch(settings.storage){
                            case 'local':
                            {
                                delete local_storage[id];
                                LoadAffiliationList();
                            }
                            break;
                            default:
                            {
                                $.ajax(
                                    {
                                        type: "GET",
                                        url: rest_urls.DeleteAffiliation + '/' + id,
                                        dataType: "json",
                                        timeout:5000,
                                        retryMax: 2,
                                        complete: function (jqXHR,textStatus) {
                                            LoadAffiliationList();
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            alert( "Request failed: " + textStatus );
                                        }
                                    }
                                );
                            }
                                break;
                        }
                    }
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });

                //edit

                $(".edit-affiliation").live('click',function(event){
                    var current = $('#Current',affiliation_form);
                    current.show();
                    var id = $(this).attr('data-id');

                    $('#editAffiliationLabel').text('Edit Affiliation');

                    switch(settings.storage){
                        case 'local':
                        {
                            var data = local_storage[id];
                            LoadAffiliationData($('#modal-edit-affiliation'), data);
                        }
                        break;
                        default:
                        {
                            $.ajax(
                                {
                                    type: "GET",
                                    url: rest_urls.GetAffiliation + '/' + id,
                                    dataType: "json",
                                    timeout:60000,
                                    retryMax: 2,
                                    success: function (data,textStatus,jqXHR) {
                                        //load data...
                                        LoadAffiliationData($('#modal-edit-affiliation'), data);
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {
                                        alert( "Request failed: " + textStatus );
                                    }
                                }
                            );
                        }
                        break;
                    }
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                });

                LoadAffiliationList();
            }
        },
        update : function() {
            LoadAffiliationList();
        },
        count:function(){
            switch(settings.storage){
                case 'local':
                        return Object.keys(local_storage).length;
                    break;
                default:
                {
                    var count = 0;
                    $.ajax(
                        {
                            async:false,
                            type: "GET",
                            url: rest_urls.AffiliationsCount,
                            contentType: "application/json; charset=utf-8",
                            dataType: "json",
                            success: function (data) {
                                count = parseInt(data);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                alert( "Request failed: " + textStatus );
                            }
                        });
                    return count;
                }
                break;
            }
        },
        local_datasource:function(){
            if(settings.storage!='local'){
                $.error( 'storage is not set on local mode!' );
                return {};
            }
            return local_storage;
        }
    };

    $.fn.affiliations = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.affiliations' );
        }
    };

    function LoadAffiliationList(){
        switch(settings.storage){
            case 'local':
                LoadLocalAffiliationList();
                break;
            default:
                LoadRemoteAffiliationList();
                break;
        }
    }

    //helper functions

    function LoadAffiliationData(dlg ,affiliation){
        $("#JobTitle", dlg).val(affiliation.JobTitle);
        $("#OrgName", dlg).val(affiliation.OrgName);
        $("#StartDate", dlg).val(affiliation.StartDate);
        if(affiliation.EndDate!='')
            $("#EndDate", dlg).val(affiliation.EndDate);
        $("#Id", dlg).val(affiliation.Id);
        $("#Current", dlg).prop('checked', affiliation.Current == 1 ? true : false);

        dlg.modal('show')

    }

    function renderAffiliationList(data){
        if (data.length > 0) {
            //remove error message
            $("label.error[for='HoneyPotForm_RegistrationForm_Affiliations']").remove();

            var template = $('<ul><li><div class="affiliation-header">' +
                '<span class="title"></span>' +
                '<span class="affiliation-actions">&nbsp;' +
                '<a href="#" class="btn btn-primary btn-xs edit-affiliation affilation-action-btn" title="Edit Affiliation">Edit</a>&nbsp;' +
                '<a href="#" class="btn btn-danger btn-xs del-affiliation affilation-action-btn" title="Delete Affiliation">Delete</a>&nbsp;' +
                '</span></div></li></ul>');

            var directives = {
                'li': {
                    'affiliation<-context': {
                        'a.edit-affiliation@data-id': 'affiliation.Id',
                        'a.del-affiliation@data-id': 'affiliation.Id',
                        'span.title':function(arg){
                            var title = "<div class='org-name'><span><b>"+ arg.item.OrgName +"</b></span></div><div class='affiliation-info'>From " + arg.item.StartDate;
                            if(arg.item.EndDate!=''){
                                title+=' To ' + arg.item.EndDate +'</div>';
                            }
                            else{
                                title+=' (Current) </div>';
                            }
                            return title;
                        }
                    }
                }
            };
            $("#affiliations-container").html(template.render(data, directives));
        }
        else{
            $("#affiliations-container").html('');
        }
    }

    function LoadLocalAffiliationList(){
        var array = [];
        if(jQuery.isEmptyObject(local_storage))
        {
            var val = $("#HiddenAffiliations").val();
            if(val != '')
            {
                local_storage = JSON.parse(val);
            }
        }
        for ( var item in local_storage ){
            array.push( local_storage[ item ] );
        }
        renderAffiliationList(array);
    }

    function LoadRemoteAffiliationList(){
        $.ajax(
            {
                type: "GET",
                url: rest_urls.ListAffiliations,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function (data) {
                    renderAffiliationList(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert( "Request failed: " + textStatus );
                },
                timeout:15000,
                retryMax: 5
            });
    };

    // End of closure.

}( jQuery ));
