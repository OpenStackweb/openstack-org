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
// rank widget
var rank_order = 0;

jQuery(document).ready(function($) {

    var form  = $('#DeploymentSurveyYourThoughtsForm_Form');
    var form_validator = null;

    $.validator.addMethod("sortable_drivers", function (value, element) {
        $.validator.messages.sortable_drivers = 'You must select at least one business driver.';
        var sorted  = $('.selected-rank', $('#catalog'));
        var count   = sorted.length;
        for(var i = 0; i < count; i++){
            var element = $(sorted[i]);
            if(element.attr('data-answer') == '6311ae17c1ee52b36e68aaf4ad066387_answer' && $('#business_drivers_other_text', element.parent() ).val()==='' ){
                $.validator.messages.sortable_drivers = 'You must specify you custom business driver.';
                return false;
            }
        }
        return count > 0;
    },'');

    form_validator = form.validate({
        rules: {
            'BusinessDrivers'  : {sortable_drivers: true },
            'InformationSources[]'  : {required: true }
        },
        ignore: [],
        invalidHandler: jqueryValidatorInvalidHandler,
        errorPlacement: function (error, element) {
            if(element.hasClass('hidden'))
            {
                error.appendTo($('#catalog'));
            }
            else
                jqueryValidatorErrorPlacement(error, element);
        }

    });

    setCustomValidationRuleForOtherText($('#DeploymentSurveyYourThoughtsForm_Form_InformationSources_Other'), $('#OtherInformationSources'));

    setCustomValidationRuleForDependantQuestion([$('#DeploymentSurveyYourThoughtsForm_Form_InterestedUsingContainerTechnology')], $('#container_related_tech'), function(chk){
        if(chk.is(':checked')){
            $('input[type="checkbox"]','#DeploymentSurveyYourThoughtsForm_Form_ContainerRelatedTechnologies').rules('add', { required:true});
        }
        else{
            $('input[type="checkbox"]','#DeploymentSurveyYourThoughtsForm_Form_ContainerRelatedTechnologies').rules('remove', 'required');
        }
    });

    form.submit(function( event ) {
        var valid   = form.valid();
        var sorted  = $('.selected-rank', $('#catalog'));
        var count   = sorted.length;
        var drivers = '';
        var current_answers = [];
        for(var i = 0; i < count; i++){
            var element = $( sorted[i]);
            var answer = element.attr('data-answer');
            var sort   = element.attr('data-sort');
            if(answer == '6311ae17c1ee52b36e68aaf4ad066387_answer'){
                var other_value = $('#business_drivers_other_text', element.parent() ).val();
                $('#DeploymentSurveyYourThoughtsForm_Form_OtherBusinessDrivers').val(other_value);
            }
            current_answers[sort] = answer;
        }
        for(var j = 1 ;j <= count; j++ ){
           var answer =  current_answers[j];
           drivers += answer_table[answer] + ',';

        }
        //remove last ,
        drivers = drivers.substring(0, drivers.length - 1);
        $('#DeploymentSurveyYourThoughtsForm_Form_BusinessDrivers').val(drivers);

        if(!valid){
            event.preventDefault();
            return false;
        }

    });



    $('.rank-text').live('click', function(evt){

        var rank = $('.rank-wrapper', $(this).parent());

        if(rank.hasClass('selected-rank')){
            //undo this rank
            console.log(rank_order);
            --rank_order;
            var current_rank =  rank.attr('data-sort');
            clearRankElement(rank);
            var sorted = $('.selected-rank', $('#catalog'));
            for (var i = 0; i < sorted.length; i++) {
                recalculateRankElement($(sorted[i]), current_rank);
            }
        }
        else if(rank_order < 5) {
            ++rank_order
            rank.text(rank_order);
            rank.attr('data-sort',rank_order);
            rank.addClass('selected-rank');
        }
    });

    $('#clear_all_business_drivers').click(function(evt){
        evt.preventDefault();
        if(window.confirm('Are you sure?')) {
            rank_order = 0;
            var sorted = $('.selected-rank', $('#catalog'));
            for (var i = 0; i < sorted.length; i++) {
                clearRankElement($(sorted[i]));
            }
        }
        return false;
    });
});

function clearRankElement($element){
    if ($element.attr('data-answer') == '6311ae17c1ee52b36e68aaf4ad066387_answer') {
        jQuery('#business_drivers_other_text', $element.parent()).val('');
    }
    $element.removeClass('selected-rank');
    $element.text('');
    $element.attr('data-sort', '');
}

function recalculateRankElement($element, old_rank){
    var current_rank = parseInt($element.attr('data-sort'));
    if(current_rank > old_rank && current_rank > 1)
        --current_rank;
    $element.text(current_rank);
    $element.attr('data-sort', current_rank);
}