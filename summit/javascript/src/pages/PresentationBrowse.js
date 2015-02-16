/**
 * @jsx React.DOM
 */
var req = require('reqwest');
var React = require('react');
var BS = require('react-bootstrap');
var Router = require('react-router');
var FullHeightScroller = require('../components/FullHeightScroller');
var ToggleButtons = require('../components/ToggleButtons');
var PresentationListItem = require('../components/PresentationListItem');
var PresentationDetailPanel = require('../components/PresentationDetailPanel');

var Grid = BS.Grid;
var Row = BS.Row;
var Col = BS.Col;

var timeout;

module.exports = React.createClass({

	displayName: 'PresentationBrowse',

	mixins: [Router.Navigation],

	getInitialState: function () {
		return {
			categoryFilter: null,
			textFilter: "",
			textFilterText: "",
			voteFilter: null,

			currentPresentation: false,
			presentationPage: 0,
			presentations: [],
			hasMore: false,
			page: 0,
			summit: null,

			loading: false,
			errorMsg: false
		};
	},

	keyUpListener: function (e) {
		if(e.keyCode === 39) {
			this.navigateAdjacentPresentation(1);
		}
		if(e.keyCode === 37) {			
			this.navigateAdjacentPresentation(-1);
		}
	},

	componentDidMount: function () {
		this.loadSummit();
		this.loadPresentations();

		if(this.props.params.presentationid) {
			this.loadPresentation(this.props.params.presentationid);
		}

		window.addEventListener("keyup", this.keyUpListener);
	},

	
	componentDidUnmount: function () {
		window.removeEventListener("keyup", this.keyUpListener);
	},



	componentDidUpdate: function (prevProps, prevState) {
		if(
			this.state.categoryFilter !== prevState.categoryFilter ||
			this.state.textFilter !== prevState.textFilter ||
			this.state.voteFilter !== prevState.voteFilter
		) {
			this.setState({
				page: 0
			}, this.loadPresentations);
		}
	},


	componentWillReceiveProps: function (newProps) {
		var params = newProps.params;
		if(!this.state.currentPresentation || params.presentationid !== this.state.currentPresentation.id) {
			return this.loadPresentation(params.presentationid);
		}
	},


	apiCall: function (endpoint, data, method) {
		this.setState({
			loading: true
		});

		return req({
		    url: 'presentations/api/v1/'+endpoint,
		    method: method || "GET",
		    type: 'json',
		    data: data
		})
		.fail(function (err, msg) {
			this.setState({
				errorMsg: msg
			})
		}.bind(this))
		.always(function () {
			this.setState({
				loading: false
			})
		}.bind(this))

	},


	loadPresentation: function (id) {		
		return this.apiCall('presentation/'+id)
			.then(function (resp) {
				this.setState({
					currentPresentation: resp
				})
			}.bind(this));
		

	},


	getPresentationIndex: function (id) {
		var index = false;
		
		if(!id) id = this.state.currentPresentation.id;

		this.state.presentations.some(function(p, i) {
			if(p.id === id) {
				index = i;
				return true;
			}
		}.bind(this));

		return index;
	},


	handleCategoryChange: function (val) {
		this.state.summit.categories.forEach(function (cat) {
			if(cat.id === val) {
				this.setState({
					categoryFilter: val,
					categoryFilterText: cat.title
				});
			}
		}.bind(this));
	},



	handleTextFilterChange: function (e) {
		this.setState({
			textFilterText: e.target.value
		});

		if(timeout) clearTimeout(timeout);
		window.setTimeout(function() {
			this.setState({
				textFilter: this.state.textFilterText
			})
		}.bind(this));
	},


	handleVoteFilterChange: function (val) {
		this.setState({
			voteFilter: val
		});
	},


	loadSummit: function () {
		return this.apiCall('summit/active')
				.then(function(resp) {			
					this.setState({
						summit: resp						
					});
				}.bind(this));
	},

	loadPresentations: function () {
		data = {
			category: this.state.categoryFilter,
			keyword: this.state.textFilter,
			voted: this.state.voteFilter,
			page: this.state.page
		};

		return this.apiCall('', data)
			.then(function(resp) {			
				this.setState({
					presentations: data.page === 0 ? resp.results : this.state.presentations.concat(resp.results),
					hasMore: resp.has_more,
					page: this.state.page+1
				});
			}.bind(this));
	},


	handleDismiss: function () {
		this.setState({
			errorMsg: false
		});
	},


	handlePresentationScroll: function (scrollTop, scrollHeight, clientHeight) {		
		if(this.state.hasMore && scrollHeight > clientHeight) {
			if(scrollHeight - scrollTop === clientHeight) {
				this.loadPresentations();
			}
		}
	},

	handleVote: function (val) {
		return this.apiCall(
			'presentation/'+this.state.currentPresentation.id+'/vote',
			{vote: val},
			'POST'
		).then(function(resp) {
			var index = this.getPresentationIndex();			
			if(index !== false) {		
				this.state.presentations[index].user_vote = val;
				this.state.currentPresentation.user_vote = val;
				this.setState({
					presentations: this.state.presentations,
					currentPresentation: this.state.currentPresentation
				});
			}
		}.bind(this));
	},


	navigateToPresentation: function (id) {
		return this.transitionTo('detail', {presentationid: id});
	},


	navigateAdjacentPresentation: function (adder) {		
		var index = this.getPresentationIndex();

		if(!adder) adder = 1;
		
		if(index) {

			index += adder;
			if(this.state.presentations[index]) {
				return this.navigateToPresentation(this.state.presentations[index].id);
			}
		}
	},




	render: function () {
		var voteOptions = [
			{value: null, label: "All"},
			{value: true, label: "Voted"},
			{value: false, label: "Not voted"}
		];

		return (
				<Grid>
					<Row>
						<Col xs={12} md={4}>
							<BS.Input onChange={this.handleTextFilterChange} value={this.state.textFilterText} type="text" placeholder="Search..." />

							<div className="category-select">
								<BS.DropdownButton title={this.state.categoryFilterText || "Choose a category..."} onSelect={this.handleCategoryChange}>
									{this.state.summit && this.state.summit.categories.map(function(cat) {
										return <BS.MenuItem key={cat.id}>{cat.title}</BS.MenuItem>								
									})}
								</BS.DropdownButton>
							</div>			
							
							
							<div className="voted-filter">
								<ToggleButtons								
									value={this.state.voteFilter}
									options={voteOptions}
									onChange={this.handleVoteFilterChange} />
							</div>


						    <FullHeightScroller scrollHandler={this.handlePresentationScroll}>
						    {this.state.presentations.map(function (item, i) { 	
						    	var current = this.state.currentPresentation;					    
						    	return (
						    	<Row key={item.id} className="presentation-list" onClick={this.navigateToPresentation.bind(null, item.id)}>
						    		<Col xs={12} md={12}>
						    			<PresentationListItem presentation={item} active={item.id == this.state.currentPresentation.id} />
						    		</Col>
						    	</Row>	
						    	);
						    }.bind(this))}
						    </FullHeightScroller>
						</Col>

						{!this.state.currentPresentation && !this.props.params.presentationid &&
							<Col xs={12} md={8}>
								<Row>
									Please choose a presentation from the left pane.
								</Row>
							</Col>
						}

						{this.state.currentPresentation &&
							<PresentationDetailPanel onVote={this.handleVote} presentation={this.state.currentPresentation} />
						}

					</Row>
				</Grid>
		);

	}
})