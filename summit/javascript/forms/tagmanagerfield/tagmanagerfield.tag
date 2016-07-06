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
                <taggroup selected_tags="{ parent.selected_tags }" group="{ group }" tags="{ tags }" each={ group, tags in allowed_tags }></taggroup>
                <hr>
                <input type="hidden" id="Tags" name="Tags" value="{ value }" />
            </div>
            <div class="col-lg-3">
                <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading">Tags You have Selected</div>
                    <div class="panel-body">
                    <h4>{ selected_tag_count } of 8 tags</h4>
                    </div>
                    <ul class="list-group" if={!canSelect()}>
                        <li class="list-group-item">You have selected the maximum number of tags.</li>
                    </ul>
                </div>
            </div>
        </div>
        <script>

            this.allowed_tags        = opts.allowed_tags;
            this.value               = opts.value;
            this.selected_tags       = (this.value) ? this.value.split(',') : new Array();
            this.selected_tag_count  = this.selected_tags.length;
            var self                 = this;

             self.on('mount', function(){
                // set the selected tags based on the value from the input field
                self.form      = $('form');
                self.validator = self.form.validate({
                    ignore: [],
                    rules: {},
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

            canSelect() {
                return self.selected_tag_count < 8;
            }

            addTag(tag) {
                self.selected_tag_count++;
                self.selected_tags.push(tag);
                self.value = self.selected_tags.join();
                self.update();
            }

            removeTag(tag) {
                self.selected_tag_count--;
                delete self.selected_tags[self.selected_tags.indexOf(tag)];
                self.value = self.selected_tags.join();
                self.update();
            }
        </script>

</tagmanagerfield>
