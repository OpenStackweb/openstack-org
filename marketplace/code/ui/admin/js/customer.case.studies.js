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
(function( $ ){

   var form  = null;
   var table = null;
   var form_validator = null;

   var methods = {
       init : function(options) {
           form = $(this);

           if(form.length>0){

               table = $("table",form);

               $.validator.addMethod("customer_case_studies_max_count", function (value, element, arg) {
                   var max_count      = arg[0];
                   var table          = arg[1];
                   var rows           = $("tbody > tr",table);
                   return rows.length <= max_count;
               }, "You reached the maximum allowed number of customer case studies.");

               $.validator.addMethod("validate_customer_case_study_name", function (value, element, arg) {
                   var table = arg[0];
                   var rows  = $("tbody > tr",table);
                   element   = $(element);
                   var length = rows.length-1;
                   if(length===0) return true;
                   var res = true;
                   for(var i=0;i < length;i++){
                       var aux_element = $('.customer-case-study-name',rows[i]);
                       res = res && !(element.attr('id') != aux_element.attr('id') && aux_element.val().trim() == value.trim());
                       if(!res) break;
                   }

                   return true;

               }, "Customer case study name already defined.");


               form_validator = form.validate({
                   rules: {
                       add_study_name  : {
                           required:true,
                           ValidPlainText:true,
                           maxlength: 125,
                           customer_case_studies_max_count:[10,table],
                           validate_customer_case_study_name:[table] },
                       add_study_link  : {  required: {
                           depends:function(){
                               $(this).val($.trim($(this).val()));
                               return true;
                           }
                       }, complete_url:true }
                   },
                   onfocusout: false,
                   focusCleanup: true,
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


               $('tbody',table).sortable(
                   {
                       items: '> tr:not(.add-customer-case-study)',
                       update: function( event, ui ) {
                           renderOrders();
                       }
                   }
               );

               $('#add-new-customer-case-study').click(function(event){
                   var is_valid = form.valid();
                   event.preventDefault();
                   event.stopPropagation();
                   if (is_valid){
                       var new_case_study = {};
                       new_case_study.name = $('#add_study_name').val();
                       new_case_study.link = $('#add_study_link').val();
                       new_case_study.id   = 0;
                       addCustomerCaseStudy(new_case_study);
                       $('#add_study_name').val('');
                       $('#add_study_link').val('');
                       form_validator.resetForm();
                   }
                   return false;
               });

               $(document).on('click',".remove-customer-case-study",function(event){
                   var remove_btn = $(this);
                   var tr = remove_btn.parent().parent();
                   var name = $('input.customer-case-study-name',tr);
                   name.rules("remove", "required");
                   name.rules("remove", "ValidPlainText");
                   name.rules("remove", "maxlength");
                   var link =  $('input.customer-case-study-link',tr);
                   link.rules("remove", "required");
                   link.rules("remove", "url");
                   tr.remove();
                   renderOrders();
                   event.preventDefault();
                   event.stopPropagation();
                   return false;
               });
           }
       },
       serialize: function(){
           //remove validator for add controls
           form_validator.settings.ignore = ".add-control";
           var is_valid = form.valid();
           //re add rules
           form_validator.settings.ignore = [];
           if(!is_valid){
               return false;
           }
           var res = [];
           var rows = $("tbody > tr",table);
           for(var i=0;i<rows.length-1;i++){
               var caseStudy = {};
               caseStudy.name = $('input.customer-case-study-name',rows[i]).val();
               caseStudy.link = $('input.customer-case-study-link',rows[i]).val();
               res.push(caseStudy);
           }
           return res;
       },
       load:function(customer_case_studies){
            for(i in customer_case_studies){
                addCustomerCaseStudy(customer_case_studies[i]);
            }
       }
   };

   $.fn.customer_case_studies = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.customer_case_studies' );
        }
   };

   //helper functions
   function renderOrders(){
        var rows = $("tbody > tr",table);
        for(var i=0;i<rows.length-1;i++){
            var th_order = $('.customer-case-study-order',rows[i]);
            th_order.text(i+1);
        }
   }

   function addCustomerCaseStudy(new_case_study){
        var rows_number = $("tbody > tr",table).length;

        var row_template = $('<tr><td class="customer-case-study-order" style="border: 1px solid #ccc;background:#eaeaea;width:5%;font-weight:bold;"></td>' +
            '<td style="border: 1px solid #ccc;width:30%;background:#fff;">' +
            '<input type="text" style="width:300px;" class="customer-case-study-name text autocompleteoff"></td>' +
            '<th style="border: 1px solid #ccc;width:30%;background:#fff;">' +
            '<input type="text" style="width:300px;" class="customer-case-study-link text autocompleteoff"></td>' +
            '<td style="border: 1px solid #ccc;background:#eaeaea;width:10%;color:#cc0000;">' +
            '<a href="#" class="remove-customer-case-study">x&nbsp;Remove</a></td></tr>>');

        var directives = {
            'td.customer-case-study-order':function(arg){ return rows_number;},
            'input.customer-case-study-name@value':'name',
            'input.customer-case-study-link@value':'link',
            'input.customer-case-study-name@id'   : function(arg){ return 'customer-case-study-name_'+(rows_number);},
            'input.customer-case-study-link@id'   : function(arg){ return 'customer-case-study-link_'+(rows_number);},
            'input.customer-case-study-name@name' : function(arg){ return 'customer-case-study-name_'+(rows_number);},
            'input.customer-case-study-link@name' : function(arg){ return 'customer-case-study-link_'+(rows_number);}
        };
        var html = row_template.render(new_case_study, directives);
        $(".add-customer-case-study",table).before(html);

        var name = $('#customer-case-study-name_'+(rows_number));
        name.rules("add",{
            required: {
                depends:function(){
                    $(this).val($.trim($(this).val()));
                    return true;
                }
        }});
        name.rules("add", { required:true });
        name.rules("add", { ValidPlainText:true });
        name.rules("add", { maxlength: 125});
        name.rules("add", { validate_customer_case_study_name:[table]});
        var link = $('#customer-case-study-link_'+(rows_number));
        link.rules("add", "required");
        link.rules("add", "complete_url");
    }
// End of closure.
}( jQuery ));