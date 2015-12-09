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
    var refresh_interval = 30000;
    var xhr = null;

    $(document).ready(function($){

        //hide job descriptions
        $('.jobDescription').hide();

        if(document.location.hash && document.location.hash != '#none') {
            var opened_job_id = document.location.hash.substring(1);
            $('.jobDescription','#' + opened_job_id).slideDown();
            $('.jobExpand','#' + opened_job_id).html('less');
        }

        // toggles the job descriptions
        $('.jobExpand').live('click',function() {
            var parent_div = $(this).closest('div.jobPosting');

            if (parent_div.find('div.jobDescription').is(":visible")) {
                document.location.hash = 'none';
                $(this).html('more');
            } else {
                document.location.hash = parent_div.attr('id');
                $(this).html('less');
            }

            parent_div.find('div.jobDescription').slideToggle(400);

            return false;
        });

        setInterval(refresh_jobs, refresh_interval);
    })

    function refresh_jobs() {
        if(xhr!=null) return;

        var foundation = (typeof($.QueryString['foundation']) != "undefined") ? 1 : 0;
        var url = 'api/v1/jobs/list?foundation='+foundation;

        xhr = $.ajax({
            type: "GET",
            url: url,
            success: function(result){

                $('.jobPosting','.job_list').remove();
                $('.job_list').append(result);

                if (document.location.hash && document.location.hash != '#none') {
                    var opened_job_id = document.location.hash.substring(1);
                    $('.jobDescription' , '#'+opened_job_id).show();
                    $('.jobExpand','#' + opened_job_id).html('less');
                } else {
                    $('.jobDescription').hide();
                }

                xhr = null;
            }
        });
    }

    // End of closure.
}( jQuery ));