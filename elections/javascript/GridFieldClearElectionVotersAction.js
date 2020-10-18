/**
 * Copyright 2018 Open Infrastructure Foundation
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

    $(".clear-election-voters").onclick

    $.entwine('ss', function($) {
        $('.ss-gridfield button.clear-election-voters.action').entwine({
            //show upload field when button is clicked
            onclick: function(e){
                if(confirm("Are you sure? this action is irreversible")){
                    this._super(e);
                    return;
                }
                e.preventDefault();
            }
        });
    });

}(jQuery));