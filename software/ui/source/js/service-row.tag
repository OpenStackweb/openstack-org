require('./t.tag');
<service-row>

    <div class="project-table-row">
        <div class="project-table-code-name">
            <a href="" onclick={ coreServiceDetails }>
                <span class="project-table-mascot-icon" style="background-image: url({mascotImage()});"></span>
                { code_name }
            </a>
        </div>
        <div class="project-table-description"><a href="" onclick={ coreServiceDetails }>{ name }</a></div>
    </div>    

    <script>

        var self = this;

        coreServiceDetails(e) {
            window.location = self.coreServiceDetailsURL();
            return false;
        }

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