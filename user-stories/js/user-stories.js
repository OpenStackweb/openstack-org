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
$(document).ready(function(){
    $("#user-stories-video-trigger").click(function () {
        $('.user-stories-video-wrapper').addClass('on');
        $("iframe.user-stories-hero-video").attr("src", $("iframe.user-stories-hero-video").attr("src").replace("autoplay=0", "autoplay=1"));
        $(this).addClass('off');
        event.preventDefault();
    });
})
