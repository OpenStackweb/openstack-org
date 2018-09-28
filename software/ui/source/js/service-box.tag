require('./t.tag');
<service-box>
    <div class="col-md-4 col-sm-6">
        <div class="core-services-single-full">
            <div class="core-top">
                <a href="{coreServiceDetailsURL()}" >
                    <div class="core-title" style="background-image: url({mascotImage()});">
                        { code_name }
                    </div>
                </a>
                <div class="core-service">
                    <a href="{coreServiceDetailsURL()}" >{ name }</a>
                </div>
            </div>
        </div>
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
</service-box>
