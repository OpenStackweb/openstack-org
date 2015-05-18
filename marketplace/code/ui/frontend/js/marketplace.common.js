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


jQuery(document).ready(function($){
    // Bootstrap stuff
    $('.services-table-wrapper i').tooltip();

    // Toggle test result list
    $("#see-results-link").click(function(evt) {
        if ($(".test-details-list").hasClass('open')) {
            $(this).text("Show Full Results [+]")
            $(".test-details-list").removeClass("open");
        } else {
            $(this).text("Hide Full Results [-]")
            $(".test-details-list").addClass("open");
        }
        evt.preventDefault();
        return false
    });
});
