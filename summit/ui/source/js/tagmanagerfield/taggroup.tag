<taggroup>
    <h3>{ opts.group }</h3>
    <clicktag label="{ tag }" tag_selected="{ parent.isSelected(tag) }" each={ tag, i in opts.tags } ></clicktag>

    <script>
        this.selected_tags = opts.selected_tags;
        var self = this;

        isSelected(tag) {
            return ($.inArray(tag,self.selected_tags) > -1);
        }

    </script>

</taggroup>