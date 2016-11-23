<raw>
    <span></span>
    this.root.innerHTML = opts.content
</raw>
<highlights>
    <div class="keynote-highlights">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>Highlights from the Keynotes</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                <div class="clicked-keynote-highlight" style="background-image:url('{ featureditem.image_url }')">
                <div class="clicked-keynote-description">
                <h4>{ featureditem.title }</h4>
                <raw content="{ featureditem.description }"/>
                </div>
                </div>
                </div>
            </div>
            <div class="row" each={ keynotes, i  }>
                <div class="keynote-highlight-row" if={ items.length > 0 }>
                    <div class="keynote-highlight-day">
                    { day }
                    </div>
                </div>
                <div class="col-sm-3" each={ items }>
                    <a href="#" class="keynote-highlight-single" onclick={ parent.setFeatured } >
                    <div class="keynote-highlight-thumb { active: featured }">
                    <img src={ preview_url } alt="">
                    </div>
                    <div class="keynote-highlight-title">
                    { title }
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>

        this.keynotes = opts.keynotes;
        var self = this;

        for(var i = 0 ; i < this.keynotes.length; i++) {
            var entry = this.keynotes[i];
            if(entry.items.length > 0) {
            // By default, set the first item to be featured
                this.featureditem = entry.items[0];
                this.featureditem.featured = true;
                break;
            }
        }

        setFeatured(e) {
            // turn off current featured item
            self.featureditem.featured = false
            // set this item as featured
            e.item.featured = true
            self.featureditem = e.item
        }
    </script>
</highlights>