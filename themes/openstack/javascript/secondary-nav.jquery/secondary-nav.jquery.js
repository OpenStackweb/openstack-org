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
(function($) {
    var default_options = {
        num : 900,
        onScroll: function(){

        }
    };

    var methods = {
        init : function(options) {
            var $this   = $(this);
            var options = $.extend({}, default_options, options);

            $('a[href^="#"]').on("click", function(e) {
                e.preventDefault();
                $(document).off("scroll");
                $("a").each(function() {
                    $(this).removeClass("active")
                });
                $(this).addClass("active");
                var target = this.hash,
                    menu = target;
                $target = $(target);
                var detla = 0;
                if ($("#nav-bar").hasClass("fixed")) {
                    detla = 60
                } else {
                    detla = 170
                }
                $("html, body").stop().animate({
                    scrollTop: $target.offset().top - detla
                }, 500, "swing", function() {
                    window.location.hash = target;
                    $(document).on("scroll", options.onScroll);
                })
            });

            $(window).bind("scroll", function() {
                if ($(window).scrollTop() > options.num) {
                    $this.addClass("fixed")
                } else {
                    $this.removeClass("fixed")
                }
            });
        }
    };

    $.fn.secondaryNav = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.secondaryNav' );
        }
    };

})(jQuery);