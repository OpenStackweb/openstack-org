<presentationitem>
	<div class="presentation-row {active: isActive()} { selected: opts.data.selected }" onclick={ setActive }>
			<div class="row">		
				<div class="{ col-lg-9: !opts.details } { col-lg-11: opts.details } { col-md-9: !opts.details } { col-md-11: opts.details }" onclick={ setActive }>
					<span class="pull-left presentation-row-icon"><i class="fa fa-check-circle-o" show={ opts.data.selected }></i>&nbsp;</span>
					<div class="presentation-title">
						{ opts.data.title }

						<span if="{ !(parent.summit.on_selection_period || parent.summit.is_selection_period_over) && opts.data.moved_to_category }" class="new-presentation"><i class="fa fa-star"></i>New</span>
					</div>
				</div>
				<div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show={ !opts.details } >
					{ opts.data.vote_average }
				</div>
				<div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show={ !opts.details }>
					{ opts.data.vote_count }
				</div>
				<div class="col-lg-1 col-md-1 hidden-sm hidden-xs" show={ !opts.details }>
					{ opts.data.total_points }
				</div>
		
			</div>
	</div>

	<style scoped>
		.presentation-row {
			border: 1px solid #D5D5D5;
			padding: 5px;
			margin-bottom: -1px;
			cursor: pointer;
			font-size: 1.3em;
		}

		.new-presentation, .new-presentation .fa {
			color: orange!important;
		}

		.presentation-row.active .new-presentation, .presentation-row.active .new-presentation .fa {
			color: white!important;
		}

		.presentation-row.selected {
			background-color: rgba(221, 239, 255, 0.50);
		}

		.presentation-row a {
			text-decoration: none;
		}

		.presentation-row.active, .presentation-row.active a {
			background-color: #3A89D3;
			color: white;
		}

		.presentation-row .fa {
			padding-top: 0.2em;
			color: #0078AE;
		}

		.presentation-row.active .fa {
			color: white;
		}

		.presentation-row-icon {
			display: block;
			width: 30px;
			padding-left: 4px;
		}

		.presentation-title {
			margin-left: 30px;
		}

	</style>

	setActive(e){
		this.parent.setActiveKey(this.opts.key)
		riot.route('presentations/show/' + this.opts.data.id)
	}
	
	isActive(){
		return this.parent.activekey == this.opts.key
	}


</presentationitem>