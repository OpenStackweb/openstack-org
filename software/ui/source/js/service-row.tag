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

        var self = this;

        coreServiceDetailsURL() {
            var url = self.parent.base_url+'releases/'+self.parent.release_id+'/components/'+self.slug;
            return url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

    </script>
</service-row>