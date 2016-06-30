import React from 'react';
import PresentationList from '../views/PresentationList';
import {connect} from 'react-redux';
import {fetchPresentations, fetchLists} from '../../actions';
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
	}

	componentDidMount() {
		if(this.props.category) {
			this.props.fetchPresentations({category: this.props.category});
			this.props.fetchLists(this.props.category);
		}
		else {
			this.props.fetchPresentations();
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.category !== this.props.category) {
			this.props.fetchPresentations({
				category: nextProps.category,
				keyword: nextProps.search,
				page: 1
			});
			if(nextProps.category) {		
				this.props.fetchLists(nextProps.category);
			}
		}
		else if(nextProps.search !== this.props.search) {			
			this.props.fetchPresentations({
				keyword: nextProps.search,
				category: nextProps.category,
				page: 1
			});
		}
		else if(nextProps.presentations && nextProps.defaultPresentation.id) {			
			if(nextProps.params.id && !nextProps.category && nextProps.defaultPresentation.category_id) {
				browserHistory.push(URL.create(undefined, {
					category: nextProps.defaultPresentation.category_id,
					search: nextProps.search
				}));
			}
			else if(!nextProps.params.id) {
				browserHistory.push(URL.create(`browse/${nextProps.defaultPresentation.id}`, {
					category: nextProps.defaultPresentation.category_id,
					search: nextProps.search
				}));
			}
		}		
	}

	requestMore() {
		this.props.fetchPresentations({
			page: this.props.currentPage+1
		});
	}

    render () {
    	if(!this.props.presentations) {
    		return <Bounce />
    	}

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
	                <PresentationList 
	                	presentations={this.props.presentations} 
	                	hasMore={this.props.hasMore}
	                	onRequestMore={this.requestMore}
	                	category={this.props.category}
	                	search={this.props.search}
	                	/>
	               </div>
                </div>
                <div className="col-md-8">
               		{this.props.children}
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
			defaultPresentation: state.detailPresentation.id ? state.detailPresentation : (results ? results[0] : null),
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
		}
	})
)(Browse);