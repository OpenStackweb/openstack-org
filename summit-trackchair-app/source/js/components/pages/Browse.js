import React from 'react';
import PresentationList from '../views/PresentationList';
import {connect} from 'react-redux';
import {fetchPresentations, fetchLists, clearPresentations} from '../../actions';
import CategoryNavigator from '../containers/CategoryNavigator';
import PresentationSearchForm from '../containers/PresentationSearchForm';
import PresentationFilterDropdown from '../containers/PresentationFilterDropdown';
import FeedItem from '../ui/FeedItem';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';
import Bounce from '../ui/loaders/Bounce';

class Browse extends React.Component {

	constructor (props) {
		super(props);
		this.requestMore = this.requestMore.bind(this);
		this.keyListener = this.keyListener.bind(this);
	}

	componentDidMount() {
		const {category, detailPresentation, params, defaultCategory, search} = this.props;
		if(category) {
			this.props.fetchPresentations({category});
			this.props.fetchLists(category);
		}
		// /browse ---> /browse?category=1
		else if(defaultCategory && !params.id) {
			browserHistory.push(URL.create(`browse`, {
				category: defaultCategory.id,
				search: search
			}));
		}

		document.addEventListener('keyup', this.keyListener);	
	}

	componentWillUnmount() {
		document.removeEventListener('keyup', this.keyListener);
	}

	componentWillReceiveProps(nextProps) {
		const {category, search, params, presentations, detailPresentation} = nextProps;

		// /browse/123?category=20  ----> /browse/123?category=30
		if(category !== this.props.category) {
			this.props.clearPresentations();
			this.props.fetchPresentations({
				category: category,
				keyword: search,
				page: 1
			});
			if(category) {
				this.props.clearPresentations();		
				this.props.fetchLists(category);
				// /browse/123?category=20 -> /browse/123?category=30 -> /browse/?category=30
				if(params.id) {
					browserHistory.push(URL.create('browse/', {
						category: category,
						search: search
					}));				
				}
			}
		}
		// /browse/123?search=foo  ----> /browse/123?search=bar
		else if(search !== this.props.search) {
			this.props.clearPresentations();	
			this.props.fetchPresentations({
				keyword: search,
				category: category,
				page: 1
			});
		}
		
		else if(presentations && detailPresentation) {			
			// /browse/123 ----> /browse/123?category=20
			if(params.id && !category && detailPresentation.category_id) {
				browserHistory.push(URL.create(undefined, {
					category: detailPresentation.category_id,
					search: search
				}));
			}
		}
	}

	keyListener(e) {		
		if(tagName === 'TEXTAREA' && tagName === 'INPUT') return;

		const adder = e.keyCode === 37 ? -1 : (e.keyCode === 39 ? 1 : 0);

		if(!adder) return;

		const {tagName} = e.target;
		const {presentations} = this.props;
		const {detailPresentation} = this.props;

		let index = presentations.findIndex(p => p.id == detailPresentation.id);
		index += adder;
		const newPresentation = presentations[index];

		if(newPresentation) {
			browserHistory.push(URL.create(
				`browse/${newPresentation.id}`,
				{
					category: this.props.category,
					search: this.props.search
				}
			));
		}

	}

	requestMore() {
		this.props.fetchPresentations({
			category: this.props.category,
			page: this.props.currentPage+1
		});
	}

    render () {
        return (
            <div>
               <div className="col-md-4">
                  <div className="ibox float-e-margins">
                  	<PresentationSearchForm />
                  	<div className="row">
                  		<div className="col-md-2">
                  			<PresentationFilterDropdown />
                  		</div>
                  		<div className="col-md-10">
                  			<CategoryNavigator />
                  		</div>
                  	</div>
                  	{this.props.presentations &&
	                <PresentationList 
	                	presentations={this.props.presentations} 
	                	hasMore={this.props.hasMore}
	                	onRequestMore={this.requestMore}
	                	category={this.props.category}
	                	search={this.props.search}
	                	/>
	                }
	                {!this.props.presentations &&
	                	<Bounce />
	                }
	               </div>
                </div>
                <div className="col-md-8">

						<div>{this.props.children}</div>

                </div>
            </div>
        );
    }

}
export default connect(
	state => {
		let {results, filter} = state.presentations;
		if(results) {
			results = results.filter(p => {
				switch(filter) {
					case 'all':
						return p.selected !== 'pass';
					case 'unseen':
						return !p.viewed;
					case 'seen':
						return !!p.viewed;
					case 'selected':
						return p.selected === 'selected';
					case 'maybe':
						return p.selected === 'maybe';
					case 'pass':
						return p.selected === 'pass';
					case 'moved':		
						return !!p.moved_to_category && !p.viewed;
					case 'team':
						return p.group_selected;
					case 'untouched':
						return !p.selected;

					default:
						return p
				}				
			})
		}
		return {
			presentations: results,
			detailPresentation: state.detailPresentation.id ? state.detailPresentation : null,
			defaultCategory: state.summit.data.categories.find(c => (
				c.user_is_chair
			)) || state.summit.data.categories[0],
			category: state.routing.locationBeforeTransitions.query.category,
			search: state.routing.locationBeforeTransitions.query.search,
			hasMore: state.presentations.has_more,
			currentPage: state.presentations.page
		}
	},
	dispatch => ({
		fetchPresentations(params) {			
			dispatch(fetchPresentations(params));
		},

		fetchLists(category) {
			dispatch(fetchLists(category));
		},

		clearPresentations() {
			dispatch(clearPresentations());
		}
	})
)(Browse);