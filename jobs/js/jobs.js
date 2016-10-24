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
    var xhr              = null;
    var sort_by          = null;
    var type             = null;
    var keywords         = null;

    function expandJobDetail(event) {
        var job_id     = $(this).data('id');
        var parent_div = $('#'+job_id);

        console.log("clicked job id "+ job_id);

        var detail_div = parent_div.find('div.jobDescription');

        var is_visible = detail_div.hasClass('is_visible');

        if (is_visible) {
            document.location.hash = '';
            $(this).html('More info');
        } else {
            document.location.hash = job_id;
            $(this).html('Less');
        }

        detail_div.toggleClass('is_visible');
        // show/ hide details
        parent_div.find('div.jobDescription').slideToggle(400);
        event.preventDefault();

        return false;
    }

    $(document).ready(function($){

        //hide job descriptions
        $('.jobDescription').hide();

        if(document.location.hash && document.location.hash != '') {
            var opened_job_id = document.location.hash.substring(1);
            $('.jobDescription','#' + opened_job_id).slideDown();
            $('.jobDescription','#' + opened_job_id).addClass('is_visible');
            $('.jobExpand','#' + opened_job_id).html('less');
        }

        // toggles the job descriptions
        $(document).on('click','.jobExpand' , expandJobDetail);

        $("#ddl-menu-sort").on('click', 'li a', function(){
            $("#sort_by").html($(this).text()+' <span class="caret"></span>');
            sort_by = $(this).attr('data-sort-by');
            refresh_jobs();
        });

        $("#ddl-menu-types").on('click', 'li a', function(){
            $("#filter_by_type").html($(this).text()+' <span class="caret"></span>');
            type = $(this).attr('data-type-id');
            refresh_jobs();
        });

        $(document).on('click', '.search-btn',function(){
            keywords = $('#txt-keywords').val();
            $('.clear-btn').show();
            refresh_jobs();
        });

        $(document).on('click', '.clear-btn',function(){
            $('#txt-keywords').val('');
            keywords = '';
            $('.clear-btn').hide();
            refresh_jobs();
        });

        $('#txt-keywords').on('keypress', function (e) {
            if(e.which === 13){
                keywords = $('#txt-keywords').val();
                $('.clear-btn').show();
                refresh_jobs();
            }
        });

        setInterval(refresh_jobs, refresh_interval);

    })

    function refresh_jobs() {
        if(xhr!=null) return;

        var url = 'api/v1/jobs/list';

        if(type != null){
            url += (url.indexOf('?') > 0?'&':'?')+'type_id='+type;
        }

        if(keywords != null && keywords != ''){
            url += (url.indexOf('?') > 0?'&':'?') +  'kw='+keywords;
        }

        if(sort_by != null){
            url += (url.indexOf('?') > 0?'&':'?')+'sort_by='+sort_by;
        }

        xhr = $.ajax({
            type: "GET",
            url: url,
            success: function(result){
                $(document).off('click','.jobExpand' , expandJobDetail);
                $('.jobPosting','.job_list').remove();
                if(result == '') result='<div class="empty-job-list"><p>There are no jobs matching your criterias!</p></div>';
                $('.job_list').html(result);
                // toggles the job descriptions
                $(document).on('click','.jobExpand' , expandJobDetail);

                if (document.location.hash && document.location.hash != '') {
                    var opened_job_id  = document.location.hash.substring(1);
                    $('.jobDescription' , '#'+opened_job_id).show();
                    $('.jobExpand','#' + opened_job_id).html('less');
                }

                xhr = null;
            }
        });
    }

    // End of closure.
}( jQuery ));