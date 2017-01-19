import React from 'react';
import Sidebar from '../containers/Sidebar';
import MobileSidebar from '../containers/MobileSidebar';
import PresentationPagination from '../containers/PresentationPagination';
import Loader from '../ui/Loader';
import TopBanner from '../ui/TopBanner';
import { 
	clearError, 
	clearPresentations, 
	requestPresentations,
	requestCategories,
	navigatePresentations
} from '../../action-creators';

import { connect } from 'react-redux';
import AnimatedPresentationDetail from './AnimatedPresentationDetail';
import MobileTools from '../views/MobileTools';

class VotingApp extends React.Component {

	constructor (props) {
		super(props);
		this._keyListener = this._keyListener.bind(this);
	}

	_getPresentations(props, offset) {
		const params = {};
		
		if(props.category) params.category = props.category;
		if(props.search) params.search = props.search;
		params.offset = offset;
		
		this.props.requestPresentations(params);
	}

	_keyListener (e) {
		const {tagName} = e.target;

		if(tagName === 'TEXTAREA' || tagName === 'INPUT') return;

	    if(e.keyCode === 37) return this.props.navigatePresentations(-1);
	    if(e.keyCode === 39) return this.props.navigatePresentations(1);
	}


	componentDidMount () {	
		this.props.requestCategories();	
		this._getPresentations(this.props, 0);

		document.addEventListener('keyup', this._keyListener);
	}


	componentWillUnmount () {
		document.removeEventListener('keyup', this._keyListener);
	}


	componentWillReceiveProps (nextProps) {
		if(
			nextProps.category !== this.props.category ||
			nextProps.search !== this.props.search
		) {
			this.props.clearPresentations();
			this._getPresentations(nextProps, 0);
		}
	}

	render () {		 
		const {
			errorMsg,
			clearError,
			xhrLoading,
			ready,
			preview,
			requestedID,
			presentationID,
			navigationDirection,
			isMobile,
			sidebar
		} = this.props;

		const filter = this.props.params.filter || 'none';
     	
     	if(preview) {
			return (
				<div className="row">
					<div className="col-lg-9 col-md-9 col-sm-12 voting-content-body-wrapper">
						<PresentationDetail />
					</div>
				</div>
			);
		}
		
		return (
			<div className="row">				
				{errorMsg &&
					<TopBanner 
						className="error"
						dismissText="That's unfortunate"
						onDismiss={clearError}>
							<strong>Yikes!</strong> {errorMsg}
					</TopBanner>
				}
				{ready &&
					<div>
						<Loader active={xhrLoading} type='spin' className='main-loader' />
						{isMobile ? <MobileSidebar filter={filter} show={sidebar} /> : <Sidebar filter={filter} />}
						<div className="voting-content-body-wrapper">
							<AnimatedPresentationDetail />
						</div>
						<MobileTools />
					</div>
				}
				{!ready &&
					<Loader type='bounce' />
				}
			</div>		
		);
	}
}


export default connect(
	(state) => {
		return {
			xhrLoading: state.ui.loading,
			errorMsg: state.ui.errorMsg,
			ready: (state.categories.initialised && state.presentations.initialised),
			category: state.presentations.category,
			search: state.presentations.search,
			preview: window.location.search.match(/^\?preview/),
			presentationID: state.presentations.selectedPresentation.id,
			requestedID: state.presentations.requestedPresentationID,
			navigationDirection: state.presentations.navigationDirection,
			sidebar: state.mobile.showPresentationList,
			isMobile: state.mobile.isMobile
		}
	},
	{ 
		clearError,
		clearPresentations,
		requestPresentations,
		requestCategories,
		navigatePresentations
	}
)(VotingApp);