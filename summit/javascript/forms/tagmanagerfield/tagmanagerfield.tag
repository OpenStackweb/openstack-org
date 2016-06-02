require('./clicktag.tag')
<tagmanagerfield>

        <div class="row">
            <div class="col-lg-12">
                <p></p>
                <div class="alert alert-info" role="alert" if={canSelect()}> Please select up to 8 tags that describe your presentation.</div>
                <div class="alert alert-warning" role="alert" if={!canSelect()}> You have selected the maximum number of tags.</div>
            </div>
        </div>

        <div class="row">
        <div class="col-lg-9">

        <h3>Topics</h3>
        <clicktag label="101"></clicktag>
        <clicktag label="App Developer"></clicktag>
        <clicktag label="Architect"></clicktag>
        <clicktag label="CxO"></clicktag>
        <clicktag label="Community"></clicktag>
        <clicktag label="Containers"></clicktag>
        <clicktag label="Enterprise"></clicktag>
        <clicktag label="HPC"></clicktag>
        <clicktag label="ISV"></clicktag>
        <clicktag label="Ops"></clicktag>
        <clicktag label="Public Clouds"></clicktag>
        <clicktag label="Security"></clicktag>
        <clicktag label="Startup"></clicktag>
        <clicktag label="Telecom"></clicktag>
        <clicktag label="Upstream"></clicktag>
        <clicktag label="User Experience"></clicktag>


        <h3>Speaker</h3>
        <clicktag label="Ambassador"></clicktag>
        <clicktag label="CxO"></clicktag>
        <clicktag label="Diversity"></clicktag>
        <clicktag label="Operator"></clicktag>
        <clicktag label="Project Technical Lead (PTL)"></clicktag>
        <clicktag label="Scientific"></clicktag>
        <clicktag label="User"></clicktag>
        <clicktag label="Women Of OpenStack"></clicktag>

        <h3>OpenStack Projects Mentioned</h3>

        <clicktag label="Astara"></clicktag>
        <clicktag label="Barbican"></clicktag>
        <clicktag label="Cinder"></clicktag>
        <clicktag label="Cloudkitty"></clicktag>
        <clicktag label="Congress"></clicktag>
        <clicktag label="Cue"></clicktag>
        <clicktag label="Designate"></clicktag>
        <clicktag label="Docs"></clicktag>
        <clicktag label="Freezer"></clicktag>
        <clicktag label="Fuel"></clicktag>
        <clicktag label="Glance"></clicktag>
        <clicktag label="Heat"></clicktag>
        <clicktag label="Horizon"></clicktag>
        <clicktag label="Infra"></clicktag>
        <clicktag label="Ironic"></clicktag>
        <clicktag label="Keystone"></clicktag>
        <clicktag label="Kolla"></clicktag>
        <clicktag label="Magnum"></clicktag>
        <clicktag label="Manila"></clicktag>
        <clicktag label="Mistral"></clicktag>
        <clicktag label="Monasca"></clicktag>
        <clicktag label="Murano"></clicktag>
        <clicktag label="Neutron"></clicktag>
        <clicktag label="Nova"></clicktag>
        <clicktag label="Oslo"></clicktag>
        <clicktag label="Rally"></clicktag>
        <clicktag label="Sahara"></clicktag>
        <clicktag label="Searchlight"></clicktag>
        <clicktag label="Security"></clicktag>
        <clicktag label="Senlin"></clicktag>
        <clicktag label="Solum"></clicktag>
        <clicktag label="Swift"></clicktag>
        <clicktag label="Telemetry"></clicktag>
        <clicktag label="Tripleo"></clicktag>
        <clicktag label="Trove"></clicktag>
        <clicktag label="Zaqar"></clicktag>  

        <hr>
        <input type="hidden" id="Tags" name="Tags" value="{ opts.value }" />
        </div>
        <div class="col-lg-3">
            <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Tags You have Selected</div>
            <div class="panel-body">
            <h4>{ tagsSelected } of 8 tags</h4>
            </div>
            <ul class="list-group" if={!canSelect()}>
            <li class="list-group-item">You have selected the maximum number of tags.</li>
            </ul>
            </div>
        </div>
        </div>
        <script>

             var self     = this
             self.on('mount', function(){
                // set the selected tags based on the value from the input field
                self.taglist   = opts.value;
                self.form      = $('form');
                self.validator = self.form.validate({
                    ignore: [],
                    rules: {
                        'Tags':{ required:true },
                        messages:
                        {
                            Tags:'You must select at least one Tag.'
                        }
                    },
                    invalidHandler: function(form, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            var element = validator.errorList[0].element;
                            var offset = (element.name == 'Tags') ? $(element).prev().offset().top : $(element).offset().top;
                            $('html, body').animate({
                                scrollTop: offset-100
                            }, 2000);
                        }
                    }
                });

            })

            self.on('update', function(){

                var selectedTagsArray = []
                self.tagsSelected = 0

                // Update list of selected tags
                for (var i = 0; i < self.tags.clicktag.length; i++) {
                    if(self.tags.clicktag[i].selected == true) {
                        selectedTagsArray.push(self.tags.clicktag[i].opts.label)
                        self.tagsSelected = self.tagsSelected + 1
                    }
                }

                // Assign the tags to the input field
                $('#Tags[type=hidden]').val( selectedTagsArray.join(", "));
            })

            canSelect() {
                return self.tagsSelected < 8
            }
        </script>

        <style>

        </style>
</tagmanagerfield>
