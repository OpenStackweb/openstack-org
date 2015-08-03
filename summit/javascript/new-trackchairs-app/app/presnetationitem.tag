<presentationitem>
	<div class="col-lg-12 presentation-row {active: isActive()} { selected: opts.data.selected }" onclick={ setActive }>
		<div class="col-lg-12 ">
			<div class="row">
				<div class="col-lg-9">
					{ opts.data.title }
				</div>
				<div class="col-lg-1" show={ !opts.details } >
					{ opts.data.vote_average }
				</div>
				<div class="col-lg-1" show={ !opts.details }>
					{ opts.data.vote_count }
				</div>
				<div class="col-lg-1" show={ !opts.details }>
					{ opts.data.total_points }
				</div>
			</div>
		</div>		
	</div>

	<style>
		.presentation-row {
			border: 1px solid #D5D5D5;
			padding: 5px;
			margin-bottom: -1px;
			cursor: pointer;
			font-size: 1.3em;
		}

		.presentation-row.selected {
			background-color: rgba(190, 222, 244, 0.44);
		}

		.presentation-row a {
			text-decoration: none;
		}

		.presentation-row.active, .presentation-row.active a {
			background-color: #4CB3D6;
			color: white;
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