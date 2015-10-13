(function ($) {

    $.entwine('ss.custom', function ($) {

        $("div.ddl-api-version-id").entwine({
            initialize: function()
            {

            },
            onmatch: function ()
            {
                console.log('version admin onmatch');

                var ddl_versions  = $('select','.ddl-api-version-id');
                var ddl_component = $('select','.ddl-os-component-id');

                ddl_component.change(function(event){
                    var component_id = $(this).val();
                    var component_versions = versions[component_id];

                    console.log('on select api version');
                    ddl_versions.empty(); //remove all child nodes
                    ddl_versions.html('');
                    ddl_versions.append("<option value='' selected='selected'>-- Please Select --</option>");
                    $.each(component_versions, function (i, item) {
                        ddl_versions.append($('<option>', {value: item.value,text : item.text}));
                    });
                    ddl_versions.trigger('liszt:updated');
                });


                var current_component = ddl_component.val();

                if(current_component != ''){
                    var current_version = ddl_versions.val();

                    //console.log('current_component '+current_component+' current_version '+current_version);

                    var component_versions = versions[current_component];
                    ddl_versions.empty(); //remove all child nodes
                    ddl_versions.html('');
                    ddl_versions.append("<option value='' selected='selected'>-- Please Select --</option>");
                    $.each(component_versions, function (i, item) {
                        ddl_versions.append($('<option>', {value: item.value,text : item.text}));
                    });
                    ddl_versions.val(current_version);
                    ddl_versions.trigger('liszt:updated');
                }
            }
        });

    });

})(jQuery);