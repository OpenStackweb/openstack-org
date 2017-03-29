require('./service-box.tag')
require('./service-row.tag')
require('./t.tag');

<service-group>
    <div class="row" id={ getGroupId(group_title) }>
        <div class="col-sm-12">
            <p class="service-section-title">
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
                <tbody>        
                    <virtual data-is="service-row"  each="{ component in components }"></virtual>
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

        coreServiceDetails(e) {
            window.location = self.coreServiceDetailsURL(e);
        }

        coreServiceDetailsURL(e) {
            var slug  = e.item.slug;
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+slug;
            return url;
        }

        mascotImage(component) {
            var slugWithoutSpaces = component.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }        

    </script>
</service-group>
