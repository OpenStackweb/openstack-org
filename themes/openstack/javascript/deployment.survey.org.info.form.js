jQuery(document).ready(function($) {
    var form = $('#DeploymentSurveyOrgInfoForm_Form');
    var form_validator = null;
    if(form.length > 0) {

        $.validator.addMethod("ValidNewOrgName", function (value, element, arg) {
            value = value.trim();
            var field = arg[0];
            if(value=='0' && field.val().trim()==='')
                return false;
            return true;
        }, "You Must Specify a New Organization Name!.");

        form_validator = form.validate({
            rules: {
                'Title'  : {required: true },
                'PrimaryCity'  : {required: true },
                'PrimaryCountry'  : {required: true },
                'UserGroupMember'  : {required: true }
            },
            onfocusout: false,
            focusCleanup: true,
            ignore: [],
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {
                    validator.errorList[0].element.focus();
                }
            }
        });

        var ddl   = $('#DeploymentSurveyOrgInfoForm_Form_OrgID',form);
        var input = $('#DeploymentSurveyOrgInfoForm_Form_Organization',form);

        if(ddl.length > 0) {
             var new_org_name = $('.new-org-name',form);
             new_org_name.hide();

            ddl.rules('add',{
                required:true,
                 ValidNewOrgName:[new_org_name]
             });

             ddl.change(function(event){
                var ddl = $(this);
                if(ddl.val()=='0'){
                    new_org_name.show();
                    form_validator.resetForm();
                }
                else{
                    new_org_name.hide();
                }
            });
        }
        else if(input.length > 0) {
            input.rules('add',{required:true});
        }

        form.submit(function( event ) {
            var valid = form.valid();
            if(!valid){
                event.preventDefault();
                return false;
            }
        });
    }
});
