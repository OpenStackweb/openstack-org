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
jQuery(document).ready(function($){

    $('table.stories').tableDnD({
        dragHandle: ".dragHandle"
    });


    $('form.UpdateStories').on('submit',function(e){
        var i = 1;
        $(this).find('.order').each(function(){
            $(this).val(i);
            i++;
        })
    });

    $('td.userStoryTitle').click(function(e){
        var target = $(e.target);
        if(!target.is('input') && !target.is('a')){
            var val = $(this).find('input[type=text]').val();
            $(this).find('span').html( val ).toggle();
            $(this).find('input').toggle();
        }
    });

    $('td.userStoryTitle').blur(function(){
        $(this).parent().find('span').html( $(this).val() );
    });

    $(document).on('click', '.unpublish_ss', function(e){
        if(!window.confirm("Are you sure that you want to unpublish this user story?")){
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        return true;
    });

});
