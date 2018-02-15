import React from 'react';
import PresentationList from '../views/PresentationList';
import {connect} from 'react-redux';
import {fetchPresentations, fetchLists, clearPresentations} from '../../actions';
import CategoryNavigator from '../containers/CategoryNavigator';
import PresentationSearchForm from '../containers/PresentationSearchForm';
import PresentationFilterDropdown from '../containers/PresentationFilterDropdown';
import PresentationTagCloud from '../containers/PresentationTagCloud';
import FeedItem from '../ui/FeedItem';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';
import Bounce from '../ui/loaders/Bounce';
import {getFilteredPresentations} from '../../selectors';
import '../../rx/presentationActions';

class Browse extends React.Component {

	constructor (props) {
		super(props);
		this.keyListener = this.keyListener.bind(this);
	}

	componentDidMount() {
		document.addEventListener('keyup', this.keyListener);	
	}

	componentWillUnmount() {
		document.removeEventListener('keyup', this.keyListener);
	}

	keyListener(e) {
		const {tagName} = e.target;		
		if(tagName === 'TEXTAREA' || tagName === 'INPUT') return;

		const adder = e.keyCode === 37 ? -1 : (e.keyCode === 39 ? 1 : 0);

		if(!adder) return;

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

    render () {
        let {search, category} = this.props;

        let exportUrl = URL.create(
            '/trackchairs/api/v1/export/presentations',
            {
                category: category,
                search: search
            },
			'/'
		);

        return (
            <div>
               <div className="col-md-4">
                  <div className="ibox float-e-margins">
                  	<PresentationSearchForm search={search}/>
                  	<div className="row category-filters">
                  		<div className="col-md-2">
                  			<PresentationFilterDropdown />
                  		</div>
						<div className="pull-left tag-cloud-filter">
							<PresentationTagCloud />
						</div>
                  		<div className="pull-left">
                  			<CategoryNavigator />
                  		</div>
						<div className="col-md-2">
							<a href={exportUrl} className="btn btn-default buttons-html5">
								<span><i className="fa fa-download" /> Export CSV</span>
							</a>
						</div>
                  	</div>
                  	{this.props.presentations &&
	                <PresentationList 
	                	presentations={this.props.presentations} 
	                	hasMore={this.props.hasMore}
	                	category={this.props.category}
	                	search={this.props.search}
	                	/>
	                }
	                {this.props.loading && this.props.currentPage === 0 &&
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
		return {
			presentations: getFilteredPresentations(state),
			detailPresentation: state.detailPresentation.id ? state.detailPresentation : null,			
			category: state.routing.locationBeforeTransitions.query.category,
			search: state.routing.locationBeforeTransitions.query.search,
			hasMore: state.presentations.has_more,
			currentPage: +state.presentations.page,
			loading: state.presentations.loading
		}
	},
	dispatch => ({
		fetchPresentations(params) {
			dispatch(fetchPresentations(params));
		},
		clearPresentations() {
			dispatch(clearPresentations());
		}
	})
)(Browse);