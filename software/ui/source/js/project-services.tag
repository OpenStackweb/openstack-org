require('./service-group.tag')

<project-services>
    <div>
        <service-group api={api} category={category} subcategories={groups} tiles="{ false }" ></service-group>
    </div>

    <script>

        this.release_id    = opts.release_id;
        this.base_url      = opts.base_url;
        this.api           = opts.api;
        this.filter_tag    = null;
        this.groups        = opts.groups;
        this.category      = opts.category;
        var self = this;

        this.on('update', function(){
            self.groups = JSON.parse(JSON.stringify(this.opts.groups));
            if (self.filter_tag) {
                for (var key in self.groups) {
                    let filtered_comp = self.groups[key].components.filter(comp => comp.capability_tags.includes(self.filter_tag));
                    if (filtered_comp.length == 0) {
                        delete(self.groups[key]);
                    } else {
                        self.groups[key].components = filtered_comp;
                    }
                }
            }
        });

        opts.api.on('filter-capability', function(tag_name) {
              self.filter_tag = tag_name;
              self.update();
        });

    </script>
</project-services>
