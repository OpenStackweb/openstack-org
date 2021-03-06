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
(function( $ ){

    $(document).ready(function(){
        $('#summitID').change(function(evt) {
            var val = $( this ).val();
            if(parseInt(val) === 0 ){
                window.alert("You need to select a valid show.");
                return;
            }
            var form = $('#form_current_summit');
            form.submit();
        });
    });
// End of closure.
}( jQuery ));
