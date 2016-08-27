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

var show_if_public = ['ProblemAddressed','AttendeesExpectedLearnt'];


$(document).ready(function(){

    var form_validator = null;

    $.validator.addMethod("text_length", function(value, element) {
        return ($.trim($(value).text()).length < 1000);
    }, "Text must be less than 1000 characters long.");


    form_validator = $('#PresentationForm_PresentationForm').validate(
    {
        ignore:[],
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        rules: {
            Title:{required: true, maxlength: 100},
            TypeID:{required: true},
            GroupID:{required: true},
            CategoryID:{required: true},
            Level:{required: true},
            ShortDescription:{required: true, text_length: true},
            "PresentationLink[1]": {url:true},
            "PresentationLink[2]": {url:true},
            "PresentationLink[3]": {url:true},
            "PresentationLink[4]": {url:true},
            "PresentationLink[5]": {url:true}
        },
        messages: {
            Title:{
                required:'Title is required.',
                maxlength: 'Title must be less than 100 characters long.'
            },
            TypeID:{ required: 'Presentation type is required.'},
            GroupID:{ required: 'Presentation category group is required.'},
            CategoryID:{ required: 'Presentation category is required.'},
            Level:{ required: 'Presentation level is required.'},
            ShortDescription:{
                required: 'Presentation abstract is required.',
                maxlength: 'Abstract must be less than 1000 characters long.'
            }
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        invalidHandler: function(form, validator) {
            if (!validator.numberOfInvalids())
                return;
            var element = $(validator.errorList[0].element);
            if(!element.is(":visible")){
                element = element.parent();
            }

            $('html, body').animate({
                scrollTop: element.offset().top
            }, 2000);
        }
    });

    $('#PresentationForm_PresentationForm_GroupID').change(function(){
        getCategories();
        toggleFields();
    });

    $('body').on('change','input[name=CategoryID][type=radio]',function () {
        $('#PresentationForm_PresentationForm_CategoryIDbis').val($(this).val());
        getExtraQuestions();
    });

    if ($('#PresentationForm_PresentationForm_CategoryIDbis').val()) {
        getCategories();
    }

    toggleFields();

    $("#PresentationForm_PresentationForm_action_savePresentationSummary").click(function(evt) {
        tinyMCE.triggerSave();
        if($("#PresentationForm_PresentationForm").valid()) {
            //Carry on
        } else {
            evt.preventDefault();
            evt.stopPropagation();
            return false;
        }
    });

});

function getCategories() {
    var summit_id = $('#PresentationForm_PresentationForm_SummitID').val();
    var group_id = $('#PresentationForm_PresentationForm_GroupID').val();
    var category_id_bis = $('#PresentationForm_PresentationForm_CategoryIDbis').val();
    var url = 'api/v1/summits/'+summit_id+'/category_groups/'+group_id+'/categories';

    if (group_id) {
        $.ajax({
            type: 'GET',
            url:  url,
            timeout:120000,
            dataType:'json',
            success: function (data, textStatus, jqXHR) {
                var html = '<label for="PresentationForm_PresentationForm_CategoryID">What is the general topic of the presentation?</label>';
                if (!$.isEmptyObject(data)) {
                    for(var key in data) {
                        var category = data[key];
                        html += '<div class="radio">';
                        html += '<input id="PresentationForm_PresentationForm_CategoryID_'+category.ID+'" class="radio" name="CategoryID" type="radio" value="'+category.ID+'">';
                        html += '<label>'+category.Html+'</label>';
                        html += '</div>';
                    }
                } else {
                    html += '<br><i>This group has no categories.</i>';
                }


                $('#category_options').html(html);

                if (category_id_bis) {
                    if ($('#PresentationForm_PresentationForm_CategoryID_'+category_id_bis).length) {
                        $('#PresentationForm_PresentationForm_CategoryID_'+category_id_bis).prop('checked',true);
                        getExtraQuestions();
                    }
                }

            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert('there was an error, please contact your administrator');
        });
    } else {
        $('#category_options').html('<br><i>This group has no categories.</i>');
    }

}

function toggleFields() {
    var is_public = $('select[name=GroupID] option:selected').hasClass('public');

    $.each(show_if_public,function(index, value) {
        if (is_public) {
            $('#'+value).show();
        } else {
            $('#'+value).hide();
        }
    });
}

function getExtraQuestions() {
    var summit_id = $('#PresentationForm_PresentationForm_SummitID').val();
    var category_id_bis = $('#PresentationForm_PresentationForm_CategoryIDbis').val();
    var presentation_id = $('#PresentationForm_PresentationForm_ID').val();
    var url = 'api/v1/summits/'+summit_id+'/categories/'+category_id_bis+'/extra_questions/'+presentation_id;

    if (category_id_bis) {
        $.ajax({
            type: 'GET',
            url:  url,
            timeout:120000,
            dataType:'json',
            success: function (data, textStatus, jqXHR) {
                $('.track-question').remove();
                $.each(data,function(idx,val){
                    $('#category_options').after(val.Html);
                });

            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            alert('there was an error, please contact your administrator');
        });
    }
}


