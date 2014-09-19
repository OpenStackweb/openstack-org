jQuery(document).ready(function ($) {

    var tabs = $("#tabs");

    if(tabs.length>0){
        tabs.tabs(
            {
                cache: false,
                beforeLoad: function (event, ui) {
                    $('.panel').hide();
                    $('#' + ui.tab.attr('data-tab')).show();
                    return false;
                }
            }
        );
        tabs.show();
    }

    $.validator.addMethod("valueNotEquals", function (value, element, arg) {
        return arg != value;
    }, "Value must not equal arg.");

    $.validator.addMethod("MaxWords", function (value, element, arg) {
        var stripped_html = value.replace(/<\/?[^>]+(>|$)/g, "");
        return value.match(/\S+/g).length <= arg.Max;
    }, "Field is invalid!");

    //company

    var company_edit_form = $('#CompanyEditForm_CompanyEditForm');

    if(company_edit_form.length>0){

        tinymce.init({
            selector: "textarea.company-description",
            resize: false,
            menubar: false,
            statusbar: false,
            setup : function(ed) {
                ed.on('change', function(event) {
                    tinymce.triggerSave();
                    $("#" + event.target.id).valid();
                });
            }
        });

        tinymce.init({
            selector: "textarea.company-contributions",
            resize: false,
            menubar: false,
            statusbar: false,
            setup : function(ed) {
                ed.on('change', function(event) {
                    tinymce.triggerSave();
                    $("#" + event.target.id).valid();
                });
            }
        });

        tinymce.init({
            selector: "textarea.company-products",
            resize: false,
            menubar: false,
            statusbar: false,
            // update validation status on change
            setup : function(ed) {
                ed.on('change', function(event) {
                    tinymce.triggerSave();
                    $("#" + event.target.id).valid();
                });
            }
        });

        company_edit_form.validate({
            ignore: [],
            rules: {
                Name: {required: true},
                URL: {required: true, url: true},
                Industry:{required: true, MaxWords:{Max:4}},
                Description:{required: true},
                Contributions:{required: true, MaxWords:{Max:150}},
                Products:{required: true, MaxWords:{Max:100}},
                ContactEmail:{email:true}
            },
            messages: {
                ContactEmail:{email:'Best Contact email address is not valid.'},
                Products:{
                    required:'Products/Services is required',
                    MaxWords:'Products/Services has more than 100 words'
                },
                Contributions:{
                    required:'How you are contributing to OpenStack is required',
                    MaxWords:'How you are contributing to OpenStack has more than 150 words'
                },
                Industry:{
                    required:'Industry is required',
                    MaxWords:'Industry has more than 4 words'
                }
            }
        });
    }
});