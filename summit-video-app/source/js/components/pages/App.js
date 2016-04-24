import React from 'react';
import NavigationBar from '../containers/NavigationBar';
import LatestUploadPanel from '../containers/LatestUploadPanel';
import VideoSearchForm from '../containers/VideoSearchForm';
import Notification from '../containers/Notification';
import ErrorMessage from '../containers/ErrorMessage';
import { fetchLatestVideo, sendNotification } from '../../actions';
import { connect } from 'react-redux';
import Animate from 'react-addons-css-transition-group';
import Helmet from 'react-helmet';
import Loader from '../ui/Loader';

class App extends React.Component {

	componentDidMount () {
		this.props.fetchLatestVideo();

		if(window.VideoAppConfig.pollInterval) {
			this._interval = window.setInterval(() => {
				this.props.fetchLatestVideo();
			}, window.VideoAppConfig.pollInterval);			
		}
	}

	comoonentWillUnmount () {		
		this._interval && window.clearInterval(this._interval);
	}

	componentDidUpdate (prevProps, prevState) {
		if(+prevProps.latestVideo.id !== +this.props.latestVideo.id) {
			const {latestVideo} = this.props;
			this.props.sendNotification(
				`A new video, "${latestVideo.title}" has been uploaded.`,
				`video/${latestVideo.slug}`
			);
		}
	}

	render () {
		const {newVideos} = this.props;
		const notificationBadge = newVideos.length ? `(${newVideos.length}) ` : '';

		return (
		<div>
			<ErrorMessage />
			<Helmet titleTemplate={`${notificationBadge} %s | OpenStack Summit Videos`} />
			<div className="video-page-hero">
				<div className="video-app-latest">
					<div className="container">
						<div className="row">
							<div className="col-sm-10 col-sm-push-1">
								{this.props.loading && !this.props.latestVideo &&
									<Loader />
								}
								{this.props.latestVideo &&
								<Animate transitionName="vertical-flip" transitionEnterTimeout={1000} transitionLeave={false}>
									<LatestUploadPanel key={this.props.latestVideo.id} video={this.props.latestVideo} />
								</Animate>
								}
							</div>
						</div>
					</div>
				</div>
				<div className="container">
					<div className="row">
						<div className="col-sm-12">
							<h1>OpenStack Videos</h1>
						</div>
					</div>
					<div className="row">
						<div className="col-sm-6 col-sm-push-3">
							<div className="search-wrapper">
								<i className="fa fa-search"></i>
								<VideoSearchForm />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div className="video-navbar-wrapper" id="video-navigation">
				<div className="container">
					<div className="row">
						<div className="col-sm-12">
							<NavigationBar className="video-navbar" />
						</div>
					</div>
				</div>
			</div>
			<div className="video-page-main" id="video-page-main">
				<div className="video-app-layout">
					{this.props.children}				
				</div>
			</div>
			<Notification />
		</div>
		);
	}
}

export default connect (
	state => {
		const {latestVideo} = state.video;
		return {
			errorMsg: state.errorMsg,
			latestVideo: latestVideo,
			loading: latestVideo && latestVideo.loading,
			newVideos: state.videos.allVideos.results.filter(v => v.isNew),
			notification: state.main.notification
		}
	},
	dispatch => ({
		fetchLatestVideo () {
			dispatch(fetchLatestVideo())
		},

		sendNotification (content, link) {
			dispatch(sendNotification({content, link}))
		}
	})
)(App);