<clicktag>
        <a href="#" class="btn btn-tag {btn-primary: selected} {btn-tag-selected: selected } {btn-default: !selected}" onclick={ selectTag }>
            <span class="pound">#</span>{ label }
        </a>

        <script>
            this.selected = opts.tag_selected;
            this.label    = opts.label;
            var self      = this;

            self.on('mount', function(){
                // see if the tag is in the tag list and should be selected
            })

            selectTag(e) {
                if (self.selected == true) {
                    self.parent.parent.removeTag(e.item.tag);
                } else if (self.parent.parent.canSelect()) {
                    self.parent.parent.addTag(e.item.tag);
                }
            }

        </script>

        <style>
            .btn {
                margin-bottom: 10px;
                margin-right: 4px;
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