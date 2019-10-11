require('./t.tag');
<service-row>

    <div class="project-table-row row">
        <div class="project-table-code-name col-xs-4">
            <a href="{coreServiceDetailsURL()}">
                <div class="row">
                    <div class="col-md-2">
                        <span class="project-table-mascot-icon" style="background-image: url({mascotImage()});"></span>
                    </div>
                    <div class="col-md-10">
                        { code_name }
                    </div>
                </div>
            </a>
        </div>
        <div class="project-table-description col-xs-8">
        <div class="row">
            <div class="col-md-12">
                <a href="{coreServiceDetailsURL()}" >{ name }</a>
            </div>

            <!--
            <div class="col-md-12 form-inline">
                <div class="component-capability" each="{ cat, tags in grouped_capability_tags }" >
                    <button class="btn btn-default btn-xs capability-cat-button" type="button" style="background-color: {getCatColor(cat)}" data-toggle="collapse" data-target="#{ id }_{ cat }_tags">
                        { cat } <i class="fa fa-caret-down"></i>
                    </button>
                </div>
                <div class="collapse capability-tags-box" each="{ cat, tags in grouped_capability_tags }" id="{ id }_{ cat }_tags">
                    <div class="capability-tag-wrapper">
                        <a href="" class="capability-tag" style="color: {getCatColor(cat)}" onclick={ selectCapability } each="{ tag in tags }">
                            {tag}
                        </a>
                    </div>
                </div>
            </div>
            -->
        </div>
        </div>
    </div>    

    <script>

        this.base_url       = opts.base_url;
        this.release_id     = opts.release_id;
        this.api            = opts.api;
        var self            = this;

        this.on('mount', function(){
            $('.capability-cat-button').click( function(e) {
                $('.collapse').collapse('hide');
            });
        });

        coreServiceDetailsURL() {
            var url = self.base_url+'releases/'+self.release_id+'/components/'+self.slug;
            return url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

        getCatColor(cat_name) {
            switch (cat_name) {
                case 'starts-from': return 'rgba(255,0,0,0.6)'; //red
                case 'technology': return 'rgba(0,128,0,0.6)'; // green
                case 'components': return 'rgba(32,178,170,0.6)'; // lightseagreen
                case 'upgrades': return 'rgba(0,0,0,0.6)'; // black
                case 'features': return 'rgba(148,0,211,0.6)'; // violet
            }
        }

        selectCapability(e) {
            let tag_name = e.item.tag;
            e.preventDefault();
            window.location.hash = tag_name;
            self.api.trigger('filter-capability', tag_name)
        }

    </script>
</service-row>