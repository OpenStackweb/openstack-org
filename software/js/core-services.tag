require('./core-service-box.tag')
<core-services>
    <div class="row">
    <div class="col-sm-12">
    <p class="service-section-title"><strong>Core Services</strong>{ components.length > 0 ? ' ( '+components.length+' Results )' : '' }</p>
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