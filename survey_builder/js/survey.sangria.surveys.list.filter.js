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
                    return question_id > 0 &&
                        $('#question_value').val() == '';
                }},
                question_value2: { required: function(){
                    return question_id > 0 &&
                        ( $('#question_value2').val() == 0 || $('#question_value2').val() == null );
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
            $('#question_value2').hide();
            $('#question_value2').val('');
            $('#question_id').val('');
            form_validator.resetForm();
            form.submit();
        });

        //search params
        var question_id = $.QueryString["question_id"];

        if(question_id != undefined) {
            var option = $('#question_id option[value="'+question_id+'"]');

            var questions_values   = option.data('values');
            var question_value_txt = $('#question_value');
            var question_value_ddl = $('#question_value2');

            if(questions_values == undefined) {
                question_value_ddl.hide();
                question_value_txt.show();

                var question_value = $.QueryString["question_value"];
                if(question_value != undefined){
                    $('#question_value').val(question_value);
                }
            }
            else{
                question_value_ddl.show();
                question_value_txt.hide();
                // populate combo box
                var items = '<option value="">-- set a question value --</option>';

                $.each(questions_values,function(key, obj)
                {
                    items+= '<option value="' + obj.id + '">' + obj.label + '</option>';
                });

                question_value_ddl.find('option').remove();
                question_value_ddl.append(items);

                var question_value2 = $.QueryString["question_value2"];
                if(question_value2 != undefined){
                    $('option[value="'+question_value2+'"]', question_value_ddl).prop('selected', true)
                }
            }
        }

        $('#question_id').change(function(){

            question_id            = $(this).val();
            var option             = $('option:selected', $(this));
            var questions_values   = option.data('values');
            var question_value_txt = $('#question_value');
            var question_value_ddl = $('#question_value2');
            form_validator.resetForm();
            question_value_txt.val('');
            question_value_ddl.find('option').remove();

            if(questions_values == undefined) {

                if (question_id === '') {
                    question_id = 0;
                }

                if (question_id > 0) {
                    question_value_txt.show();
                }
                else {
                    question_value_txt.hide();

                }

                question_value_ddl.hide();
            }
            else{
                // populate combo box
                var items = '<option value="">-- set a question value --</option>';

                $.each(questions_values,function(key, obj)
                {
                    items+= '<option value="' + obj.id + '">' + obj.label + '</option>';
                });

                question_value_ddl.append(items);

                if (question_id > 0) {
                    question_value_ddl.show();
                }
                else {
                    question_value_ddl.hide();
                }
                question_value_txt.hide();
            }
        });

        $('#btn_apply_survey_list_filter').click(function(){
            var is_valid = form_validator.valid();
            if (!is_valid) return;
            form.submit();
        });
});

