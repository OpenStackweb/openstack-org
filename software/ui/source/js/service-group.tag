require('./service-box.tag')
require('./t.tag');

<service-group>
    <div class="row" id={ getGroupId(group_title) }>
        <div class="col-sm-12">
            <p class="service-section-title">
                <span if={ opts.tiles }>Tiles Mode</span>
                <strong>
                    <t entity="Software.SERVICES_SECTION_TITLE" text={ group_title } />
                </strong>
                { ' ( '+components.length+' Results )' }
            </p>
        </div>
        <div class="col-sm-12">
            <service-box each="{ components }" tiles="{ tiles }" ></service-box>
        </div>
    </div>

    <script>

        this.base_url = this.parent.base_url;
        var self = this;
        var tiles = this.opts.tiles;

        getGroupId(group_title) {
            var group_split = group_title.split(/[ ,]+/);
            return group_split[0].toLowerCase();
        }

    </script>
</service-group>
