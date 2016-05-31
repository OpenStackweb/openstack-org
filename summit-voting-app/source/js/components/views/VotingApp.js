import React from 'react';
import Sidebar from './Sidebar';
import DevTools from '../containers/DevTools';
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
			children,
			ready,
			preview
		} = this.props;

		if(preview) {
			return (
				<div className="row">
					<div className="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
						{children}
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
						<Sidebar />
						<div className={`col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper`}>
							{children}
						</div>
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
	state => {
		return {
			xhrLoading: state.ui.loading,
			errorMsg: state.ui.errorMsg,
			ready: (state.categories.initialised && state.presentations.initialised),
			category: state.router.location.query.category,
			search: state.router.location.query.q,
			preview: state.router.location.query.preview
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