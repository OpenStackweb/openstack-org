require('./t.tag');
<service-row>
    <div class="col-md-4 col-sm-6">
    <div class="core-services-single-full">
    <div class="core-top">
    <div class="core-title" style="background-image: url({mascotImage()});">
        <a href="{ coreServiceDetailsURL() }">{ code_name }</a>
    </div>
    <div class="core-service">
        <a href="{ coreServiceDetailsURL() }">{ name }</a>
    </div>
    </div>

    </div>
    </div>

    <script>

        var self = this;

        coreServiceDetails(e) {
            window.location = self.coreServiceDetailsURL();
        }

        coreServiceDetailsURL() 
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+self.slug;
            return url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

    </script>
</service-row>
