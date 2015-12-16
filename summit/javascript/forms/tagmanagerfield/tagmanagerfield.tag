require('./clicktag.tag')
<tagmanagerfield>

        <div class="row">
            <div class="col-lg-9">
                <p></p>
                <div class="alert alert-info" role="alert" if={canSelect()}> Please select up to 8 tags that describe your presentation.</div>
                <div class="alert alert-warning" role="alert" if={!canSelect()}> You have selected the maximum number of tags.</div>
            </div>
        </div>

        <div class="row">
        <div class="col-lg-6">
        <h3>Projects Used</h3>
        <clicktag label="Nova"></clicktag>
        <clicktag label="Heat"></clicktag>
        <clicktag label="Swift"></clicktag>
        <clicktag label="Horizon"></clicktag>
        <clicktag label="Magnum"></clicktag>
        <hr/>
        <h3>Audience Type</h3>
        <clicktag label="Enterprise"></clicktag>
        <clicktag label="Upstream"></clicktag>
        <clicktag label="Architect"></clicktag>
        <clicktag label="Telco"></clicktag>
        <clicktag label="App Development"></clicktag>
        <hr/>
        <h3>Community</h3>
        <clicktag label="International"></clicktag>
        <clicktag label="Women of OpenStack"></clicktag>
        <clicktag label="Community Building"></clicktag>
        <clicktag label="Open Development"></clicktag>
        <hr>
        <input type="hidden" id="Tags" name="Tags" value="{ opts.value }" />
        </div>
        <div class="col-lg-3">
        <p></p>
        <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Tags You've Selected</div>
        <div class="panel-body">
        <h4>{ tagsSelected } of 8 tags</h4>
        </div>
        <ul class="list-group" if={!canSelect()}>
        <li class="list-group-item">You have selected the maximum number of tags.</li>
        </ul>
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