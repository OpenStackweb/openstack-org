require('./service-group.tag')

<project-services>

    <button onclick={ toggleTileMode }>Toggle View Mode</button>

    <service-group each="{ group_title, components in groups }" tiles="{ tileMode }"></service-group>

    <script>

        this.tileMode            = true;
        this.groups              = opts.groups;
        this.base_url            = opts.base_url;
        this.max_maturity_points = opts.max_maturity_points;

        var self = this;

        getCurrentReleaseId() {
            return $('#openstack_releases option:selected').text().toLowerCase();
        }

        toggleTileMode() {
            self.tileMode = !self.tileMode;
            self.update();
        }

        opts.api.on('loaded-components-by-release',function(data) {            
            self.groups =  data;
            self.update();
        });

    </script>
</project-services>
