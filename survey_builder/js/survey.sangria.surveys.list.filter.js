/**
 * Copyright 2015 OpenStack Foundation
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
$(document).ready(function() {

        var form = $('#surveys_list_filter_form');
        var question_id = 0;

        var form_validator = form.validate({
            rules: {
                survey_template_id  : { required: true},
                question_value: { required: function(){
                    return question_id > 0;
                }}
            },
            onfocusout: false,
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    validator.errorList[0].element.focus();
                }
            }
        });

        $('#survey_template_id').change(function(){
            $('#question_value').hide();
            $('#question_value').val('');
            $('#question_id').val('');
            form_validator.resetForm();
        });

        $('#question_id').change(function(){
            question_id = $(this).val();
            var question_value_txt = $('#question_value');
            question_value_txt.val('');

            if(question_id === '') {
                question_id = 0;
                form_validator.resetForm();
            }

            if(question_id > 0 ) {
                question_value_txt.show();
            }
            else{
                question_value_txt.hide();
            }
        });

        $('#btn_apply_survey_list_filter').click(function(){
            var is_valid = form_validator.valid();
            if (!is_valid) return;
            form.submit();
        });
});