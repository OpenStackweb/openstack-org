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

jQuery(document).ready(function($) {

    setStep('yourthoughts');

    var form  = $('#DeploymentSurveyYourThoughtsForm_Form');
    var form_validator = null;

    form_validator = form.validate({
        rules: {
            'BusinessDrivers[]'  : {required: true },
            'InformationSources[]'  : {required: true }
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: jqueryValidatorErrorPlacement
    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyYourThoughtsForm_Form_InformationSources_Other'), $('#OtherInformationSources'));


    $( "#catalog li" ).draggable({
        appendTo: "body",
        helper: "clone",
        cursor : "move",
        revert: true
    });

    $( "#options ol" ).droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)",
        drop: function( event, ui ) {
            $(this).find(".placeholder").remove();

            if($('li',$(this)).length < 5) {
                var answers = $(this);
                var draggable = ui.draggable;
                var answer  = $("<li class='ui-state-default ui-sortable-handle'>"+ draggable.text()+"&nbsp;<a href='#' class='remove_answer' title='remove it'>[x]</a></li>");
                var el = answer.appendTo(this);

                $('.remove_answer', answer).click(function(evt){
                    evt.preventDefault();
                    el.remove();
                    if($('li',answers).length == 0){
                        $('<li class="placeholder">Add your answers here</li>').appendTo(answers);
                    }
                    draggable.removeClass('hidden');
                    return false;
                });
                draggable.addClass('hidden');
            }
        }
    }).sortable({
        items: "li:not(.placeholder)",
        sort: function() {
        // gets added unintentionally by droppable interacting with sortable
        // using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
            $( this ).removeClass( "ui-state-default" );
        }
    });

});