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
jQuery(document).ready(function($){

    var form = $("#NewsRequestForm_NewsRequestForm");
    var form_validator = null;

    if(form.length > 0){

        $('#NewsRequestForm_NewsRequestForm_date').datetimepicker({
        });

        $('#NewsRequestForm_NewsRequestForm_date_embargo').datetimepicker({
        });

        $('#NewsRequestForm_NewsRequestForm_date_expire').datetimepicker({
        });

        //main form validation
        jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
        }, "Please specify a valid phone number (ie 333-333-4444)");

        form_validator = form.validate({
            onfocusout: true,
            focusCleanup: true,
            rules: {
                submitter_phone:{required: true, phoneUS:true},
                headline:{required: true},
                summary:{required: true},
                tags:{required: true},
                date:{required: true}
            },
            messages: {
                submitter_phone:{
                    required:'Phone is required.'
                }
            },
            focusInvalid: false,
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
            },
            errorPlacement: function(error, element) {
                if(!element.is(":visible")){
                    element = element.parent();
                }
                error.insertAfter(element);
            }
        });

    }

    var allowed_keys = [8, 13, 16, 17, 18, 20, 33, 34, 35,36, 37, 38, 39, 40, 46];

    tinyMCE.init({
        theme: "advanced",
        mode : "textareas",
        theme_advanced_toolbar_location: "top",
        theme_advanced_buttons1: "formatselect,|,bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,outdent,indent,separator,bullist,link,undo,redo,code",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        plugins : "paste",
        paste_text_sticky : true,
        setup : function(ed) {
            ed.onInit.add(function(ed) {
                ed.pasteAsPlainText = true;
            });
            ed.onKeyDown.add(function(ed, evt) {
                var key = evt.keyCode;
                var max_chars = $(tinyMCE.get(tinyMCE.activeEditor.id).getElement()).attr('max_chars');
                if(allowed_keys.indexOf(key) == -1 && max_chars){
                    var text_length = ed.getContent({ 'format' : 'text' }).length;
                    if ( text_length+1 > max_chars){
                        evt.preventDefault();
                        evt.stopPropagation();
                        return false;
                    }
                }
            });
            ed.onPaste.add(function(ed, evt) {
                var max_chars = $(tinyMCE.get(tinyMCE.activeEditor.id).getElement()).attr('max_chars');
                if (max_chars) {
                    $(ed.getBody()).text(ed.getContent({ 'format' : 'text' }).substr(0,max_chars));
                }

            });
        },
        force_br_newlines : true,
        force_p_newlines : false,
        height: "250px",
        width: "800px"
    });

    //build image popup for present image
    var image_name = $('.name','#Image').html();
    var image = '<img src="assets/news-images/'+image_name+'">';
    $('.ss-uploadfield-item-preview.preview','#Image').popover({placement: 'right', content: image, html: true, trigger: 'hover', container: 'body'});

    // rebuild preview popup when image uploaded
    $('.ss-uploadfield-files.files').on("DOMSubtreeModified",function(){
        var image_element = $('span','.ss-uploadfield-item-preview.preview').html();
        if (image_element != '') {
            var image_name = $('.name','#Image').html();
            var image = '<img src="assets/news-images/'+image_name+'">';
            $('.ss-uploadfield-item-preview.preview','#Image').popover({placement: 'right', content: image, html: true, trigger: 'hover', container: 'body'});
        }
    });

});
