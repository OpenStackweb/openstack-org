require('./t.tag');
<service-row>

    <div class="project-table-row">
        <div class="project-table-code-name">
            <a href="{coreServiceDetailsURL()}">
                <span class="project-table-mascot-icon" style="background-image: url({mascotImage()});"></span>
                { code_name }
            </a>
        </div>
        <div class="project-table-description"><a href="{coreServiceDetailsURL()}" >{ name }</a></div>
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