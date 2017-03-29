require('./t.tag');
<service-box>

    <tr> 
        <td class="project-table-code-name">
                <a href="{ coreServiceDetailsURL() }"><span class="project-table-mascot-icon" style="background-image: url({mascotImage()});"></span>
            { code_name }</a>
        </td>
        <td class="project-table-description"><a href="{ coreServiceDetailsURL() }">{ name }</a></td>
    </tr>

    <script>

        var self = this;

        coreServiceDetailsURL() {
            var url = self.parent.base_url+'releases/'+self.parent.parent.getCurrentReleaseId()+'/components/'+self.slug;
            return url;
        }

        mascotImage() {
            var slugWithoutSpaces = self.slug.replace(/ /g,"_");
            return '/software/images/mascots/' + slugWithoutSpaces + '.png';
        }

    </script>
</service-box>
