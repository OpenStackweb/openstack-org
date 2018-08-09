require('./t.tag');
<service-box>
    <div class="col-md-4 col-sm-6">
        <div class="core-services-single-full">
            <div class="core-top">
                <a href="" onclick={ coreServiceDetails }>
                    <div class="core-title" style="background-image: url({mascotImage()});">
                        { code_name }
                    </div>
                </a>
                <div class="core-service">
                    <a href="" onclick={ coreServiceDetails }>{ name }</a>
                </div>
            </div>
        </div>
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
</service-box>
