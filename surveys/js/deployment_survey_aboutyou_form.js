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

jQuery(document).ready(function($) {

    var li = $('.valNone_of_these', '#DeploymentSurveyAboutYouForm_Form_OpenStackActivity');
    if(li.length > 0 ){
        var chk  = $('.checkbox', li);
        chk.click(function(e){
           if(chk.is(':checked')){
               $('#OpenStackRelationship').removeClass('hidden');
               $('#DeploymentSurveyAboutYouForm_Form_OpenStackRelationship').removeClass('hidden');
           }
            else{
               $('#OpenStackRelationship').addClass('hidden');
               $('#DeploymentSurveyAboutYouForm_Form_OpenStackRelationship').addClass('hidden');
           }
        });
    }

    setStep('aboutyou');
});
