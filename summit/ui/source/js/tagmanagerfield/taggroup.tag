<taggroup>
    <h3>{ opts.group }</h3>
    <clicktag label="{ tag.tag }" tag_selected="{ parent.isSelected(tag.id) }" each={ tag, i in opts.tags } ></clicktag>

    <script>
        this.selected_tags = opts.selected_tags;
        var self = this;

        isSelected(tag_id) {
            return ($.inArray(tag_id, self.selected_tags) > -1);
        }

    </script>

</taggroup>