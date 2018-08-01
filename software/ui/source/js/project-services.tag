require('./service-group.tag')

<project-services>


    <div class="toggle-wrapper" onclick={ toggleTileMode }>
        <span>List View</span>
        <div class="toggle {tiles: tileMode}"></div>
        <span>Tiles View</span>
    </div>

    <div if={ Object.keys(groups).length > 0 }>
        <service-group category={activeCat.category} subcategories={activeCat.subcategories} tiles="{ tileMode }"></service-group>
    </div>

    <div if={ Object.keys(groups).length == 0 }> No Components for this search </div>

    <script>

        this.tileMode            = opts.tilemode;
        this.groups              = opts.groups;
        this.base_url            = opts.base_url;
        this.max_maturity_points = opts.max_maturity_points;
        this.activeCat           = this.groups[Object.keys(this.groups)[0]];

        var self = this;

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

        opts.api.on('change-category',function(categoryId) {
              self.activeCat = self.groups[categoryId];
              self.update();
        });

    </script>
</project-services>
