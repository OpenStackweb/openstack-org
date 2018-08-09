require('./service-group.tag')

<project-services>

    <div if={ Object.keys(groups).length > 0 }>
        <service-group category={category} subcategories={groups} tiles="{ false }"></service-group>
    </div>

    <div if={ Object.keys(groups).length == 0 }> No Components for this search </div>

    <script>

        console.log(opts.groups);

        this.groups        = opts.groups;
        this.category      = opts.category;
        var self = this;

    </script>
</project-services>
