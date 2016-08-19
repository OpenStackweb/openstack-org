/**
 * Copyright 2016 OpenStack Foundation
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

    $('#event_shortcut_go').click(function(){
        var event_id = $("#event_shortcut").val();
        if ($.isNumeric(event_id)) {
            window.location.href = admin_url+"/events/"+event_id;
        } else {
            swal("Wrong ID","Please enter a valid Event ID.","warning");
        }
    });

    $("#event_shortcut").keydown(function (e) {

        if (e.keyCode == 13) {
            e.preventDefault();
            $('#event_shortcut_go').click();
        }
    });


});