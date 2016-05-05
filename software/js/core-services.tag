require('./core-service-box.tag')
require('./t.tag');
<core-services>
    <div class="row">
    <div class="col-sm-12">
    <p class="service-section-title">
    	<strong>
    		<t entity="Software.CORE_SERVICES">Core Services</t>
    	</strong>{ ' ( '+components.length+' Results )' }</p>
    </div>
    </div>
    <div class="row">
        <core-service-box each="{ components }" ></core-service-box>
    </div>

    <script>

        this.components           = opts.components;
        this.base_url            = opts.base_url;
        this.max_maturity_points = opts.max_maturity_points;

        var self = this;

        getCurrentReleaseId() {
            return $('#openstack_releases option:selected').text().toLowerCase();
        }

        opts.api.on('loaded-components-by-release',function(data) {
            console.log('components loaded');
            self.components =  data.core_components;
            self.update();
        });

    </script>
</core-services>