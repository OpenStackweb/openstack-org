<clicktag>
        <a href="#" class="btn btn-tag {btn-primary: selected} {btn-tag-selected: selected } {btn-default: !selected}" onclick={ selectTag }>
            <span class="pound">#</span>{ opts.label }
        </a>

        <script>
            var self = this
            self.selected = this.parent.opts.value.toLowerCase().indexOf(opts.label.toLowerCase()) != -1;

            self.on('mount', function(){
                // see if the tag is in the tag list and should be selected
                if(self.parent.taglist.indexOf(self.opts.label) > -1) {
                    self.selected = true
                    self.update()
                }
            })

            selectTag() {
                if (self.parent.canSelect() == true && self.selected == false) {
                    self.selected = true
                    self.parent.update()
                } else {
                    self.selected = false
                    self.parent.update()
                }
            }

        </script>

        <style>
            .btn {
                margin-bottom: 10px;
            }
            .pound {
                opacity: 0.5;
            }
            .btn-tag , .btn-tag:visited, .btn-tag:hover {
                color: #000000 !important;
                text-decoration: none;
            }
            .btn-tag-selected, .btn-tag-selected:visited, .btn-tag-selected:hover {
                color: #FFFFFF !important;
            }
        </style>

</clicktag>