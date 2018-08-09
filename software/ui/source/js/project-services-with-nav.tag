require('./service-group.tag')

<project-services-with-nav>

    <div if={ Object.keys(groups).length > 0 }>
        <service-group category={activeCat.category} subcategories={activeCat.subcategories} tiles="{ false }" ></service-group>
    </div>

    <div if={ Object.keys(groups).length == 0 }> No Components for this search </div>

    <script>

        this.release_id    = opts.release_id;
        this.base_url      = opts.base_url;
        this.groups        = opts.groups;
        this.activeCat     = this.groups[Object.keys(this.groups)[0]];

        var self = this;

        console.log(opts);

        opts.api.on('loaded-components-by-release',function(data) {            
            self.groups =  data;
            self.update();
        });

        opts.api.on('change-category',function(categoryId) {
              self.activeCat = self.groups[categoryId];
              self.update();
        });

    </script>
</project-services-with-nav>
