require('./service-group.tag')

<project-services>
    <service-group each="{ group_title, components in groups }" ></service-group>

    <script>

        this.groups              = opts.groups;
        this.base_url            = opts.base_url;
        this.max_maturity_points = opts.max_maturity_points;

        var self = this;

        getCurrentReleaseId() {
            return $('#openstack_releases option:selected').text().toLowerCase();
        }

        opts.api.on('loaded-components-by-release',function(data) {            
            self.groups =  data;
            self.update();
        });

    </script>
</project-services>
