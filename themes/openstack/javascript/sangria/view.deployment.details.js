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

    $('.addDeploymentBtn').click(function(){
        $('.addDeploymentForm').fadeIn();
        $('.addDeploymentForm').find('input[name=label]:first').focus();
        return false;
    });

    var form = $('#seach_deployments');

    $('.addDeploymentBtn').click(function(event){
        $('.addDeploymentForm').fadeIn();
        $('.addDeploymentForm').find('input[name=label]:first').focus();
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $("#date-from").datepicker({ dateFormat: "yy-mm-dd",autoSize: true  });
    $( "#date-to").datepicker({ dateFormat: "yy-mm-dd",autoSize: true  });

    $("#date-from").change(  function() {
        form_validator.resetForm();
        $( "#date-to").val($(this).val());
    });

    $("#date-to").change(  function() {
        form_validator.resetForm();
    });

    var date_to   = $.QueryString["date-to"];
    var date_from = $.QueryString["date-from"];
    var free_text = $.QueryString["free-text"];

    if(date_to!="undefined"){
        $("#date-to").val(date_to);
    }

    if(date_from!="undefined"){
        $("#date-from").val(date_from);
    }

    if(free_text!="undefined"){
        $("#free-text").val(free_text);
    }

    $.urlParam = function(name){
        var results = new RegExp("[\\?&]" + name + "=([^&#]*)").exec(window.location.href);
        if (results==null){
            return null;
        }
        else{
            return results[1] || 0;
        }
    }

    if($.urlParam("dep")){
        var anchor = $("#dep" + $.urlParam("dep"));
        $("html, body").animate({
            scrollTop: anchor.offset().top - 30
        }, 2000);
        anchor.parents("tr").css("background-color","lightyellow");
    }

    //main form validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        rules: {
            'date-from'  : {dpDate: true},
            'date-to'    : {dpDate: true, dpCompareDate:'ge #date-from'}
        },
        focusInvalid: false,
        invalidHandler: function(form, validator) {
            if (!validator.numberOfInvalids())
                return;
            var element = $(validator.errorList[0].element);
            if(!element.is(":visible")){
                element = element.parent();
            }

            $('html, body').animate({
                scrollTop: element.offset().top
            }, 2000);
        },
        errorPlacement: function(error, element) {
            if(!element.is(":visible")){
                element = element.parent();
            }
            error.insertAfter(element);
        }
    });

    form.submit(function(event){
        var is_valid = form.valid();
        if(!is_valid){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    })

    $('#range').change(function(event){
        var range = $(this).val();
        $('#survey_range').val(range);
        $("#range_form").submit();
    });

});
