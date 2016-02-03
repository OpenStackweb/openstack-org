<openstack-config-samples-types-nav>
    <!-- Projects Subnav -->
    <div class="container">

        <div class="outer-project-subnav">
        <div class="sample-config-slider-control left">
        <i id="config-left" class="fa fa-caret-left"></i>
        </div>
        <div class="sample-configs-slider">
        <ul class="sample-configs-subnav">
            <li class="sample_config_type" each={ configuration_types_menu_list } id="{ 'sample_config_type_'+id }" >
                <a id="{ 'sample_config_type_link_'+id }" href="#" data-id="{ id }" onclick={ selectedConfigSampleType }>{ type }</a>
            </li>
        </ul>
        </div>
        <div class="sample-config-slider-control right">
        <i id="config-right" class="fa fa-caret-right"></i>
        </div>
        </div>

    </div>

    <script>

        this.configuration_types           = opts.configuration_types;
        this.default_configuration_type    = opts.default_configuration_type;
        this.configuration_types_menu_list = opts.configuration_types_menu_list;
        this.ctrl                          = riot.observable();
        var self                           = this;

        this.on('mount', function(){

            $( document ).ready(function() {
                if(self.default_configuration_type !== null) {
                     $('#sample_config_type_link_'+ self.default_configuration_type).trigger('click');
                }
            });

        });

        selectedConfigSampleType(e) {
            var type_id = e.item.id;
            console.log('config type selected '+type_id);
            $('.sample_config_type').removeClass('active');
            $('#sample_config_type_'+type_id).addClass('active');
            self.ctrl.trigger('selected-config-sample-type', type_id);
        }

        module.exports = this.ctrl;
    </script>

</openstack-config-samples-types-nav>