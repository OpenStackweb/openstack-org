require('./service-box.tag')
require('./service-row.tag')
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
        <div class="col-sm-12" if="{ opts.tiles }">
            <service-box each="{ components }" ></service-box>
        </div>
        <div class="col-sm-12" if="{ !opts.tiles }">
            <table class="table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Service</th>                        
                    </tr>
                </thead>
                <tbody>
                    <tr><td>one</td><td>two</td></tr>
                    <tr data-is='service-row' each="{ component in components }" component={ component } ></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>

        this.base_url = this.parent.base_url;
        var self = this;

        getGroupId(group_title) {
            var group_split = group_title.split(/[ ,]+/);
            return group_split[0].toLowerCase();
        }

    </script>
</service-group>
