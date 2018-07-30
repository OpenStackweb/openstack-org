require('./service-group.tag')

<project-services>


    <div class="toggle-wrapper" onclick={ toggleTileMode }>
        <span>List View</span>
        <div class="toggle {tiles: tileMode}"></div>
        <span>Tiles View</span>
    </div>

    <service-group each="{ group_title, subcategories in groups }" tiles="{ tileMode }"></service-group>

    <script>

        this.tileMode            = opts.tilemode;
        this.groups              = opts.groups;
        this.base_url            = opts.base_url;
        this.max_maturity_points = opts.max_maturity_points;

        var self = this;

        getCurrentReleaseId() {
            return $('#openstack_releases option:selected').text().toLowerCase();
        }

        toggleTileMode() {
            self.tileMode = !self.tileMode;
            if (self.tileMode)
                window.location.hash = 'tiles';
            else
                history.pushState("", document.title, window.location.pathname + window.location.search);

            self.update();
        }

        opts.api.on('loaded-components-by-release',function(data) {            
            self.groups =  data;
            self.update();
        });

    </script>
</project-services>
