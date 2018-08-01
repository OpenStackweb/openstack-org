<t>
	<span></span>


	<script>

        this.entity         = opts.entity;
        this.text           = opts.text;
        this.root.innerHTML = ss.i18n._t(this.entity, this.text);
        var self = this;

        this.on('update', function(){
            self.root.innerHTML = ss.i18n._t(this.opts.entity, this.opts.text);
        });

    </script>
</t>


