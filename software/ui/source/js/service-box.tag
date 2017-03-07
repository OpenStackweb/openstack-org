require('./t.tag');
<service-box>
    <div class="col-md-4 col-sm-6">
    <div class="core-services-single-full" onclick={ coreServiceDetails }>
    <div class="core-top">
    <div class="core-title" style="background-image: url({mascotImage()});">
    { code_name } 
    </div>
    <div class="core-service">
    { name }
    </div>
    </div>

    </div>
    </div>

    <script>

        var self = this;

        coreServiceDetails(e) {
            var slug  = e.item.slug;
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+slug;
            window.location = url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

    </script>
</service-box>
