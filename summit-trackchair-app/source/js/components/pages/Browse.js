import React from 'react';
import PresentationList from '../views/PresentationList';
import {connect} from 'react-redux';
import {fetchPresentations} from '../../actions';
import CategoryDropdown from '../containers/CategoryDropdown';
import PresentationSearchForm from '../containers/PresentationSearchForm';
import FeedItem from '../ui/FeedItem';

class Browse extends React.Component {

	constructor (props) {
		super(props);
		this.requestMore = this.requestMore.bind(this);
	}

	componentDidMount() {
		if(this.props.category) {
			this.props.fetch({category: this.props.category});
		}
		else {
			this.props.fetch();
		}
	}

	componentWillReceiveProps(nextProps) {
		if(nextProps.category !== this.props.category) {
			this.props.fetch({
				category: nextProps.category,
				page: 1
			});
		}
		else if(nextProps.search !== this.props.search) {
			this.props.fetch({
				keyword: nextProps.search,
				page: 1
			});
		}
	}

	requestMore() {
		this.props.fetch({
			page:this.props.currentPage+1
		});
	}

    render () {
    	if(!this.props.presentations) {
    		return <div>loading</div>
    	}

        return (
            <div>
               <div className="col-lg-4">
                  <div className="ibox float-e-margins">
                  	<PresentationSearchForm />
                  	<CategoryDropdown />
	                <PresentationList 
	                	presentations={this.props.presentations} 
	                	hasMore={this.props.hasMore}
	                	onRequestMore={this.requestMore}
	                	/>
	               </div>
                </div>
                <div className="col-lg-8">
               		{this.props.children}
                </div>
            </div>
        );
    }

}
export default connect(
	state => ({
		presentations: state.presentations.results,
		category: state.routing.locationBeforeTransitions.query.category,
		search: state.routing.locationBeforeTransitions.query.search,
		hasMore: state.presentations.has_more,
		currentPage: state.presentations.page
	}),
	dispatch => ({
		fetch(params) {			
			dispatch(fetchPresentations(params))
		}
	})
)(Browse);