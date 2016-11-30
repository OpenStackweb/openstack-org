import React from 'react';
import URL from '../../utils/url';
import { connect } from 'react-redux';
import SummitItem from '../containers/SummitItem';
import GalleryPanel from '../ui/GalleryPanel';
import { fetchSummits } from '../../actions';
import FadeAnimation from '../ui/FadeAnimation';
import Loader from '../ui/Loader';
import Helmet from 'react-helmet';

class Summits extends React.Component {
	
	componentDidMount () {
		if(!this.props.summits.length) {
			this.props.fetchSummits();	
		}
	}

	render () {
		if(this.props.loading) {
			return <Loader />;
		}
		return (
			<div>
				<Helmet title="Summits" />
				<div className="container">
					<div className="row">
						<div className="video-app-summit-videos">
							<GalleryPanel className="video-panel">
								<FadeAnimation>
									{this.props.summits.map(summit => (
										<SummitItem key={summit.id} summit={summit} />
									))}						
								</FadeAnimation>
							</GalleryPanel>
						</div>
					</div>
				</div>
			</div>
		);		
	}	
}

export default connect (
	state => {
		return {
			summits: state.summits.results,
			loading: state.summits.loading
		}
	},
	dispatch => ({
		fetchSummits () {
			dispatch(fetchSummits());
		}
	})
)(Summits);