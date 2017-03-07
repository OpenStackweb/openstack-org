require('./t.tag');
<service-row>
    <td>{ opts.component.code_name }</td>
    <td>{ opts.component.name }</td>
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
</service-row>