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

    function checkQuestionVisibilityCheckBox(chk_group, question_container)
    {
        var show = false;
        $.each(chk_group, function(index , chk){
            show = show || chk.is(':checked');
        });
        setQuestionVisibility(show, question_container)
    }

    function checkQuestionVisibilityDropDown(ddl, question_container, values)
    {
        var show = false;
        $.each(values, function(index , val)
        {
            var selected_values = ddl.val();
            if(Array.isArray(selected_values))
            {
                for(var i=0;i<selected_values.length;i++)
                {
                    show = show || parseInt(val) === parseInt(selected_values[i]);
                }
            }
            else
            {
                show = show || parseInt(selected_values) === parseInt(val);
            }
        });
        setQuestionVisibility(show, question_container)
    }

    function checkQuestionVisibilityRanking(ranking_group, question_container){
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
            //clean controls
            question_container.find('input').val('');
            question_container.find('select').val('');
            question_container.find("input[type='radio']").prop('checked', false);
            question_container.find("input[type='checkbox']").prop('checked', false);
            question_container.find('textarea').val('');
            question_container.find('select.chosen').removeClass('chosen-visible');
        }
    }

    var methods = {

        init: function(){

        },

        addRequiredAnswer4CheckAbleGroup: function(chk_group, question_container){

            $.each(chk_group, function(index , chk){
                chk.click(function (e) {
                    checkQuestionVisibilityCheckBox(chk_group, question_container);
                });
            });

            checkQuestionVisibilityCheckBox(chk_group, question_container);
        },

        addRequiredAnswer4SelectAbleGroup : function(select_group, question_container){

            $.each(select_group, function(index , entry){
                entry.ddl.change(function (e) {
                    checkQuestionVisibilityDropDown(entry.ddl, question_container, entry.values);
                });
                checkQuestionVisibilityDropDown(entry.ddl, question_container, entry.values);
            });
        },

        addRequiredAnswer4RankAbleGroup: function(ranking_group, question_container){

            $('body').on('rank', function(evt, selected){
                var current = $(evt.target);
                $.each(ranking_group, function(index , ranking){
                    if($(ranking).attr('id') === current.attr('id')){
                        checkQuestionVisibilityRanking(ranking_group, question_container);
                    }
                });
            });
            checkQuestionVisibilityRanking(ranking_group, question_container);
         },

        addRequiredAnswer4TableGroup: function(table_group, question_container){

            $('body').on('table_clear', function(evt, selected){
                var current = $(evt.target);

                $.each(table_group, function(index , entry){
                    var radio_class = '.radio_' + entry.value;
                    setQuestionVisibility($(radio_class, entry.field).is(':checked'), question_container);
                });
            });

            $.each(table_group, function(index , entry){
                var radio_class = '.radio_' + entry.value;

                $(radio_class, entry.field).live('change', function (e) {
                    setQuestionVisibility($(this).is(':checked'), question_container)
                });

                setQuestionVisibility($(radio_class, entry.field).is(':checked'), question_container);
            });
        }
    };

    $.fn.survey_validation_rules = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.survey_validation_rules' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));