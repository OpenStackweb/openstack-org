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

        var form        = $('#surveys_list_filter_form');
        var question_id = 0;

        var form_validator = form.validate({
            rules: {
                survey_template_id  : { required: true},
                question_text_value: { required: function(){
                    return question_id > 0 &&
                        $('#question_text_value').val() == '';
                }},
                'question_select_values[]': { required: function(){
                    return question_id > 0 &&
                        ( $('#question_select_values').val() == 0 || $('#question_select_values').val() == null );
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
            $('#question_text_value').hide();
            $('#question_text_value').val('');
            $('#question_select_values').hide();
            $('#question_select_values').val('');
            $('#question_id').val('');
            form_validator.resetForm();
            form.submit();
        });

        //search params
        var question_id = $.QueryString["question_id"];
        var survey_lang = $.QueryString["survey_lang"];
        if(survey_lang != undefined){
            $('#survey_lang').val(survey_lang);
        }
        if(question_id != undefined) {
            var option = $('#question_id option[value="'+question_id+'"]');

            var questions_values   = option.data('values');
            var question_value_txt = $('#question_text_value');
            var question_value_ddl = $('#question_select_values');

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
                var items = '';

                $.each(questions_values,function(key, obj)
                {
                    items+= '<option value="' + obj.id + '">' + obj.label + '</option>';
                });

                question_value_ddl.find('option').remove();
                question_value_ddl.append(items);

                var question_values = $.QueryString["question_select_values[]"];
                if(question_values != undefined){
                    if( Object.prototype.toString.call( question_values ) === '[object Array]' ){
                        for (let element of question_values){
                            $('option[value="'+element+'"]', question_value_ddl).prop('selected', true)
                        }
                    }
                    else{
                         $('option[value="'+question_values+'"]', question_value_ddl).prop('selected', true)
                    }
                }
            }
        }

        $('#question_id').change(function(){

            question_id            = $(this).val();
            var option             = $('option:selected', $(this));
            var questions_values   = option.data('values');
            var question_value_txt = $('#question_text_value');
            var question_value_ddl = $('#question_select_values');
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
                var items = '';

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

