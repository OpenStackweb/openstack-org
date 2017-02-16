require('./service-box.tag')
require('./t.tag');

<service-group>
    <div class="row">
        <div class="col-sm-12">
            <p class="service-section-title">
                <strong>
                    <t entity="Software.SERVICES_SECTION_TITLE" text={ group_title } />
                </strong>
                { ' ( '+components.length+' Results )' }
            </p>
        </div>
        <div class="col-sm-12">
            <service-box each="{ components }" ></service-box>
        </div>
    </div>

    <script>

        this.base_url = this.parent.base_url;
        var self = this;

    </script>
</service-group>
