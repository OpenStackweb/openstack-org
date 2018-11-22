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
            <a href="{coreServiceDetailsURL()}" >{ name }</a>
        </div>
    </div>    

    <script>

        this.base_url   = opts.base_url;
        this.release_id = opts.release_id;
        var self        = this;

        coreServiceDetailsURL() {
            var url = self.base_url+'releases/'+self.release_id+'/components/'+self.slug;
            return url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

    </script>
</service-row>