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
    var xhr = null;
    var refresh_interval = 10000;

    jQuery(document).ready(function($){

        //hide job descriptions
        $('.jobDescription').hide();

        if(document.location.hash) {
            $('#' + document.location.hash.substring(1) + '.jobPosting div.jobDescription').slideDown();
        }

        // toggles the job descriptions
        $('a.jobTitle').live('click',function() {
            $(this).closest('div.jobPosting').find('div.jobDescription').slideToggle(400);
            return false;
        });

        setInterval(refresh_jobs, refresh_interval);

    })



    function refresh_jobs() {
        if(xhr!=null) return;
        var foundation = $.QueryString['foundation'];
        var query_string = (typeof(foundation) != "undefined") ? '?foundation=1':'';
        xhr = $.ajax({
            type: "POST",
            url: 'JobHolder_Controller/AjaxDateSortedJobs'+query_string,
            success: function(result){
                jQuery('.jobPosting','.job_list').remove();
                jQuery('.job_list').append(result);
                jQuery('.jobDescription').hide();
                xhr = null;
            }
        });
    }

    // End of closure.
}( jQuery ));