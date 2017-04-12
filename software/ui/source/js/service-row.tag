require('./t.tag');
<service-row>

    <div class="project-table-row">
        <div class="project-table-code-name">
                <a href="{ coreServiceDetailsURL() }"><span class="project-table-mascot-icon" style="background-image: url({mascotImage()});"></span>
            { code_name }</a>
        </div>
        <div class="project-table-description"><a href="{ coreServiceDetailsURL() }">{ name }</a></div>
    </div>

    <script>

        this.base_url = this.parent.base_url;
        var self = this;

        getGroupId(group_title) {
            var group_split = group_title.split(/[ ,]+/);
            return group_split[0].toLowerCase();
        }

        coreServiceDetails(e) {
            window.location = self.coreServiceDetailsURL(e);
        }

        coreServiceDetailsURL(e) {
            var slug  = e.item.slug;
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+slug;
            return url;
        }

        mascotImage(component) {
            var slugWithoutSpaces = component.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        } 

    </script>
</service-row>
