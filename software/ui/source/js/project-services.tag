require('./service-group.tag')

<project-services>

    <div if={ Object.keys(groups).length > 0 }>
        <service-group category={category} subcategories={groups} tiles="{ false }" ></service-group>
    </div>

    <div if={ Object.keys(groups).length == 0 }> No Components for this search </div>

    <script>

        this.release_id    = opts.release_id;
        this.base_url      = opts.base_url;
        this.groups        = opts.groups;
        this.category      = opts.category;
        var self = this;

    </script>
</project-services>
