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

        $('#NewsRequestForm_NewsRequestForm_date_embargo').datetimepicker({
            format: 'm/d/Y h:i a',
            defaultDate: new Date(),
            formatDate: 'm/d/Y',
            defaultTime: new Date(),
            formatTime: 'h:i a'
        });

        $('#NewsRequestForm_NewsRequestForm_date_expire').datetimepicker({
            format: 'm/d/Y h:i a'
        });

        //main form validation

        jQuery.validator.setDefaults({
            ignore: ''
        });

        jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
        }, "Please specify a valid phone number (ie 333-333-4444)");

        form_validator = form.validate({
            onfocusout: function(element) {
                $(element).valid()
            },
            focusCleanup: true,
            rules: {
                submitter_phone:{required: true},
                headline:{required: true},
                summary:{required: true},
                date_embargo:{required: true, date:true},
                city:{required: true},
                state:{required: true},
                country:{required: true},
                body:{required: true},
                tags:{required: true},
                link:{url:true},
                submitter_first_name:{required: true},
                submitter_last_name:{required: true},
                //submitter_company:{required: true},
                submitter_email:{required: true}
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
                    scrollTop: element.offset().top - 40
                }, 2000);
            },
            errorPlacement: function(error, element) {
                if(!element.is(":visible")){
                    element = element.parent();
                }
                error.insertAfter(element);
            }
        });

        $("#NewsRequestForm_NewsRequestForm_action_saveNewsArticle").click(function(evt) {
            tinyMCE.triggerSave();
            if($("#myform").valid()) {
                //Carry on
            } else {
                evt.preventDefault();
                evt.stopPropagation();
                return false;
            }
        });

    }

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

var allowed_keys = [8, 13, 16, 17, 18, 20, 33, 34, 35,36, 37, 38, 39, 40, 46];

function TinyMCENewsPasteProcess(pl, o) {
    var tmp = $('<div />', {
        html: o.content
    })
    var $elements = tmp.find("*").not("a,br");

    for (var i = $elements.length - 1; i >= 0; i--) {
        var e = $elements[i];
        $(e).replaceWith(e.innerHTML);
    }

    o.content = tmp.html();
}

function OnSetupTinyMCENewsForm (ed) {

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
        var content = ((evt.originalEvent || evt).clipboardData || window.clipboardData).getData('Text');

        if (max_chars) {
            evt.preventDefault();
            if (content.length > parseInt(max_chars)) {
                alert('Summary is too long!');
                return false;
            } else {
                ed.execCommand('mceInsertContent', false, content);
            }
        }
    });
}
