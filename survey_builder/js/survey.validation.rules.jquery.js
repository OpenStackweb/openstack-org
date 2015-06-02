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

    //private methods

    function checkGroupOtherContainerVisibility(chk_group, question_container){
        var show = false;
        $.each(chk_group, function(index , chk){
            show = show || chk.is(':checked');
        });
        setQuestionVisibility(show, question_container)
    }

    function checkOtherTextVisibilityDropDown(ddl, question_container, label){
        var show = ddl.val() == label;
        setQuestionVisibility(show, question_container)
    }

    function checkOtherTextVisibilityRanking(ranking_group, question_container){
        var show = false;
        $.each(ranking_group, function(index , ranking){
            show = show || ranking.hasClass('selected-rank');
        });
        setQuestionVisibility(show , question_container)
    }

    function setQuestionVisibility(show, question_container){
        if (show) {
            question_container.removeClass('hidden');
            question_container.find('*').removeClass('hidden');
            question_container.find('select.chosen').trigger("chosen:updated");
            question_container.find('select.chosen').addClass('chosen-visible');
        }
        else {
            question_container.addClass('hidden');
            question_container.find('*').addClass('hidden');
            question_container.find('input').val('');
            question_container.find('textarea').val('');
            question_container.find('select.chosen').removeClass('chosen-visible');
        }
    }

    var methods = {

        init: function(){

        },

        addRequiredAnswer4ClickableGroup: function(chk_group, txt_container){

            $.each(chk_group, function(index , chk){
                chk.click(function (e) {
                    checkGroupOtherContainerVisibility(chk_group, txt_container);
                });
            });

            checkGroupOtherContainerVisibility(chk_group, txt_container);
        },

        addRequiredAnswer4SelectableGroup : function(select_group, txt_container, label){

            $.each(select_group, function(index , select){
                select.change(function (e) {
                    checkOtherTextVisibilityDropDown(select, txt_container, label);
                });
                checkOtherTextVisibilityDropDown(select, txt_container, label);
            });
        },

        addRequiredAnswer4RankingGroup: function(ranking_group, txt_container){

            $('body').on('rank', function(evt, selected){
                var current = $(evt.target);
                $.each(ranking_group, function(index , ranking){
                    if($(ranking).attr('id') === current.attr('id')){
                        checkOtherTextVisibilityRanking(ranking_group, txt_container);
                    }
                });
            });

            checkOtherTextVisibilityRanking(ranking_group, txt_container);
            
         }
    };

    $.fn.survey_validation_rules = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.survey_validation_rules' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));