require('./service-box.tag')
require('./service-row.tag')
require('./t.tag');

<service-group>
    <div class="row" id={ getGroupId(group_title) }>
        <div class="col-sm-12">
            <p class="category-section-title">
                <strong>
                    <t entity="Software.SERVICES_SECTION_TITLE" text={ group_title } />
                </strong>
            </p>
        </div>
        <div class="col-md-12">
            <div class="row" each="{ subcat_title, components in subcategories }" >
                <div class="col-sm-12">
                    <p class="service-section-title">
                        <strong>
                            <t entity="Software.SERVICES_SECTION_TITLE" text={ subcat_title } />
                        </strong>
                        { ' ( '+components.length+' Results )' }
                    </p>
                </div>
                <div class="col-sm-12" show="{ opts.tiles }">
                    <service-box each="{ components }" ></service-box>
                </div>
                <div class="col-sm-12" show="{ !opts.tiles }">
                    <service-row each="{ components }"></service-row>
                </div>
            </div>
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
