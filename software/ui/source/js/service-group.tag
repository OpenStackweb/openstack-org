require('./service-box.tag')
require('./service-row.tag')
require('./t.tag');

<service-group>
    <div class="row">
        <div class="col-sm-12 all-projects-wrapper">
            <h3><t entity="Software.SERVICES_SECTION_TITLE" text={ category.Name } /></h3>
            <p><t entity="Software.SERVICES_SECTION_DESCRIPTION" text={ category.Description } /></p>
            <p></p>
        </div>
    </div>
    <div class="row" id={ "cat_" + category.ID }>
        <div class="col-md-12">
            <div class="row" each="{ subcatId, subcategory in subcategories }" >
                <div class="col-sm-12">
                    <p class="service-section-title">
                        <strong>
                            <t entity="Software.SERVICES_SECTION_TITLE" text={ subcategory.category.Name } />
                        </strong>
                        { ' ( '+subcategory.components.length+' Results )' }
                    </p>
                </div>
                <div class="col-sm-12" show="{ opts.tiles }">
                    <service-box each="{ subcategory.components }" ></service-box>
                </div>
                <div class="col-sm-12" show="{ !opts.tiles }">
                    <service-row each="{ subcategory.components }"></service-row>
                </div>
            </div>
        </div>

    </div>

    <script>

        this.base_url       = this.parent.base_url;
        this.category       = opts.category;
        this.subcategories  = opts.subcategories;
        var self = this;

        this.on('update', function(){
            self.category = this.opts.category;
            self.subcategories = this.opts.subcategories;
        });

    </script>
</service-group>
