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
// Open header drop downs on hover
jQuery(document).ready(function($){

    if ($(window).width() > 767) {
        $('ul.navbar-main li ul.dropdown-menu').addClass('dropdown-hover');
        $("ul.navbar-main li").on("mouseenter",function() {
          $(this).find('.dropdown-hover').stop(true, true).delay(400).fadeIn(100);
      }).on("mouseleave", function() {
          $(this).find('.dropdown-hover').stop(true, true).delay(100).fadeOut(200);
      });
    } else {
        $('ul.navbar-main li ul.dropdown-menu').removeClass('dropdown-hover');
    }

    // Close header search bar
    $("body").on("click", ".ossw-search-bar-close", function() {
        $(".navbar-main").toggleClass("show");
        $(".search-container").removeClass("show")
        $(".search-icon").toggleClass("show");
    });

    // Open header search bar
    $(".search-icon").on("click", function() {
        $(".navbar-main").toggleClass("show");
        $(".search-container").toggleClass("show");
        $("input", '.openstack-search-bar').first().focus();
        $(".search-icon").toggleClass("show");
    });

    $(window).on("resize", function () {
        if ($(window).width() > 767) {
            $('ul.navbar-main li ul.dropdown-menu').addClass('dropdown-hover');
            $('ul.navbar-main li').hover(function() {
                $(this).find('.dropdown-hover').stop(true, true).delay(400).fadeIn(100);
            }, function() {
                $(this).find('.dropdown-hover').stop(true, true).delay(100).fadeOut(200);
            });
        } else {
            $('ul.navbar-main li ul.dropdown-menu').removeClass('dropdown-hover');
        }
        if ($(window).width() < 1050) {
            $('#search-label').text('');
        } else {
            $('#search-label').text('Search');
        }
    });

    // Remove Search text in smaller browser windows
    if ($(window).width() < 1050) {
        $('#search-label').text('');
    } else {
        $('#search-label').text('Search');
    }
	
    // Toggle mobile header nav dropdowns
    $("i.mobile-expand").on("click", function (event) {
        if ($(this).closest('li').hasClass("open")) {
            $('li').removeClass('open');
        } else {
            $('li').not(this).closest('li').removeClass('open');
            $(this).closest('li').toggleClass('open');
        }
        event.preventDefault();
    });
});

// Remove open class when window is resized
jQuery(window).on("resize",function () {
    if (jQuery(window).width() > 767) {
        $("li").removeClass("open");
    }
});

// Global Nav UI Tracking

jQuery(document).ready(function($) {
    $(document).on("click", ".project___1BAp9", function(){
        var href  = $(this).attr('href');
        ga('send', 'event', {
            eventCategory: 'Global UI Navigation',
            eventAction: 'Click',
            eventLabel: href
        });
    });
});

jQuery(document).ready(function($) {
    $(document).on("click", "#vancouver-banner-btn", function(){
        var href  = $(this).attr('href');
        ga('send', 'event', {
            eventCategory: 'Summit Banner',
            eventAction: 'Click',
            eventLabel: href
        });
    });
});