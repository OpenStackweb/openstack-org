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

    // rank widget
    var form       = null;
    var control    = null;
    // This is the easiest way to have default options.
    var settings = {
        // These are the defaults.
        max_rank_items: 5,
        rank_order: 0
    };

    function clickRank(evt){
        var rank = $('.rank-wrapper', $(this).parent());
        var selected  = false;
        if(rank.hasClass('selected-rank')){
            --settings.rank_order;
            var current_rank =  rank.attr('data-sort');
            clearRankElement(rank);
            var sorted = $('.selected-rank', control);
            for (var i = 0; i < sorted.length; i++) {
                recalculateRankElement($(sorted[i]), current_rank);
            }
        }
        else if(settings.rank_order < settings.max_rank_items) {
            ++settings.rank_order
            rank.text(settings.rank_order);
            rank.attr('data-sort',settings.rank_order);
            rank.addClass('selected-rank');
            selected = true;
        }
        rank.trigger('rank', [selected]);
    }

    function clearRankElement($element){
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

    //private methods
    var methods = {

        init: function(options){

            settings = $.extend(settings , options);

            control = $(this);

            form    = $('.survey_step_form');

            $('.rank-wrapper').live('click', clickRank );
            $('.rank-text').live('click', clickRank );

            $('.clear_all_ranking_options', control).click(function(evt){
                evt.preventDefault();
                if(window.confirm('Are you sure?')) {
                    settings.rank_order = 0;
                    var sorted = $('.selected-rank', control);
                    for (var i = 0; i < sorted.length; i++) {
                        clearRankElement($(sorted[i]));
                        sorted.trigger('rank', false);
                    }
                }
                return false;
            });

            form.submit(function (evt){
               // serialize
                var sorted  = $('.selected-rank', control);
                var count   = sorted.length;
                var values = '';
                var current_answers = {};
                for(var i = 0; i < count; i++){
                    var element           = $(sorted[i]);
                    var answer            = element.attr('data-answer');
                    var sort              = element.attr('data-sort');
                    current_answers[sort] = answer;
                }
                for(var j = 1 ;j <= count; j++ ){
                    var answer =  current_answers[j];
                    values   +=  answer + ',';
                }
                //remove last ,
                values = values.substring(0, values.length - 1);
                $('.ctrl_hidden_value', control ).val(values);
            });
        }
    };

    $.fn.survey_ranking_field = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.survey_ranking_field' );
        }
    };

    //helper functions

// End of closure.
}( jQuery));