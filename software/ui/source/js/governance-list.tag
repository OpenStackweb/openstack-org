require('./service-row.tag')

<governance-list>

    <div if={ Object.keys(components).length > 0 }>
        <service-row each="{ components }" base_url="{ base_url }" release_id="{ release_id }" ></service-group>
    </div>

    <div if={ Object.keys(components).length == 0 }> No Components found </div>

    <script>

        this.release_id    = opts.release_id;
        this.base_url      = opts.base_url;
        this.components    = opts.components;
        var self = this;

    </script>
</governance-list>
