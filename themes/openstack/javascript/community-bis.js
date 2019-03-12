/**
 * Copyright 2017 Openstack Foundation
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

    var selected = '';

    $('#accordion-parent').on('show.bs.collapse','.collapse', function() {
        //$('#accordion-parent').find('.collapse.in').collapse('hide');
    });

    $('.nav-button').on("click", function(e){
        $('.nav-button-icon', '.nav-button').not($('.nav-button-icon', this)).removeClass('active');
        $('.nav-button-icon', this).toggleClass('active');
        $('#accordion-parent').find('.collapse.in').collapse('hide');
        $('.clear-groups').click();
    });

    $('.project-group-button').on("click", function(e){
        e.preventDefault();

        $('.project-groups-selected').slideDown('slow');
        $('.project-groups').slideUp('slow');
        selected = $(this).html();
        $('.project-group-button-selected').html(selected);

    });

    $('.clear-groups').on("click", function(e){
        e.preventDefault();
        selected = '';
        $('.project-groups-selected').slideUp('slow');
        $('.project-groups').slideDown('slow');
        $('.project-options').removeClass('in');
    });

    $('.ambassador').on("mouseenter", function(){
        $('.ambassador-twitter-veil', this).show();
    }).on("mouseleave", function() {
        $('.ambassador-twitter-veil', this).hide();
    });

});
