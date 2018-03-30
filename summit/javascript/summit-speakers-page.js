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

$(window).on("scroll", function(e) {
    var nav_char = $('#nav-char');
    var windows_scroll_top = $(window).scrollTop();
    if (windows_scroll_top > 147) {
        nav_char.css({top: 0, position:'fixed'});
    }
    else if(windows_scroll_top < 20){
        $('.char-link').removeClass('active');
        $('.char-link').addClass('inactive');
    }
    else {
        nav_char.css({top: 0, position:'relative'});
    }
});

$(function() {
   $('.char-link').live('click', function(event){
       $('.char-link').removeClass('active');
       $('.char-link').addClass('inactive');
       $(event.target).addClass('active');
   });
});