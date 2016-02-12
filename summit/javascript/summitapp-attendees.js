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

$(document).ready(function(){
    var items_per_page = 20;
    var total_pages = Math.ceil(total_attendees / items_per_page);
    var hash = $(window).url_fragment('getParams');
    var current_page = 1;

    if(!$.isEmptyObject(hash)){
        current_page = hash['page'];
    }

    var options = {
        bootstrapMajorVersion:3,
        currentPage: current_page ,
        totalPages: total_pages,
        numberOfPages: 10,
        onPageChanged: function(e,oldPage,newPage){
            var summit_id = $('#summit_id').val();
            $.getJSON('api/v1/summits/'+summit_id+'/attendees',{page:newPage, items:items_per_page},function(data){
                var output = '';
                for(var i in data) {
                    var attendee = data[i];
                    output = output + '<tr><td>'+attendee.member_id+'</td><td>'+attendee.name+'</td>';
                    output = output + '<td>'+attendee.email+'</td><td>'+attendee.ticket_bought+'</td>';
                    output = output + '<td>'+attendee.checked_in+'</td>';
                    output = output + '<td><a href="'+attendee.link+'" class="btn btn-default btn-sm active" role="button">Edit</a></td></tr>';
                }
                $('tbody','#attendees-table').html(output);
            });
        }
    }

    $('#attendees-pager').bootstrapPaginator(options);
});

