import React from 'react';
import { connect } from 'react-redux';
import { fetchVideoDetail, viewVideo } from '../../actions';
import Loader from '../ui/Loader';
import RouterLink from '../containers/RouterLink';
import ShareButton from '../ui/ShareButton';
import Helmet from 'react-helmet';

class VideoDetail extends React.Component {

	constructor (props) {		
		super(props);
		this._timeout = null;
	}

	componentDidMount () {
		if(!this.props.video || this.props.video.slug != this.props.params.slug) {
			this.props.fetchVideoDetail();
			this.setTimeout();
		}
		else if (this.props.video) {
			this.setTimeout();
		}
	}

	componentWillReceiveProps (nextProps) {
		if(!this.props.loading) {
			if(!this.props.video || (nextProps.video && nextProps.video.slug !== nextProps.params.slug)) {				
				this.props.fetchVideoDetail(nextProps.params.slug);
				this.setTimeout();
			}
		}
	}

	componentWillUnmount () {
		if(this._timeout) {
			window.clearTimeout(this._timeout);
		}
	}

	setTimeout () {
		const config = window.VideoAppConfig;		
		if(config.videoViewDelay) {
			if(this._timeout) {
				window.clearTimeout(this._timeout);
			}
			this._timeout = window.setTimeout(
				this.props.setVideoViewed,
				config.videoViewDelay
			);
		}
	}

	render () {
		const {video} = this.props;
		if(!video) {
			return <Loader />;
		}
		return (
			<div className="video-detail">
				<Helmet title={video.title} />
				<div className="video-embed">
					<iframe 
						width={853} 
						height={480} 
						src={`https://www.youtube.com/embed/${video.youtubeID}`} 
						frameBorder={0}
						allowFullScreen
					 />
				</div>
				<div className="container">
					<div className="row">
						<div className="col-sm-8">
							<h3>{video.title}</h3>

							<div className="single-video-description" dangerouslySetInnerHTML={{__html: video.description}} />
						</div>
						<div className="col-sm-4">
							<div className="video-detail-panel">
								<div className="detail-panel-section">
									<h5 className="section-title">Speakers</h5>
									<ul className="video-speakers-list">
									{video.speakers && video.speakers.map(s => (
										<li key={s.id}><RouterLink link={`speakers/show/${s.id}`}>{s.name}</RouterLink></li>
									))}
									</ul>
								</div>
                                {video.summit &&
                                <div className="detail-panel-section">
                                    <h5 className="section-title">Summit</h5>
                                    <RouterLink link={`summits/show/${video.summit.id}`}>
                                        {video.summit.title}
                                    </RouterLink>
                                </div>
                                }
								<div className="detail-panel-section">
									<h5 className="section-title">Views</h5>									
									{video.views}
								</div>
                                {video.slides &&
                                    <div className="detail-panel-section">
                                        <h5 className="section-title">Slides</h5>
                                        <a href={video.slides}>Download</a>
                                    </div>
                                }

								<div className="detail-panel-section">
									<h5 className="section-title">Share</h5>
									<ul className="videos-share-list">
										{['twitter','facebook','linkedin','google-plus'].map(service => (
											<li key={service}><ShareButton service={service} /></li>
										))}
									</ul>
								</div>
							</div>
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
			video: state.videoDetail.video,
			loading: state.videoDetail.loading
		}
	},
	(dispatch, ownProps) => ({
		fetchVideoDetail (id) {
			const videoID = id || ownProps.params.slug;
			dispatch(fetchVideoDetail(videoID));
		},

		setVideoViewed (id) {
			dispatch(viewVideo(
				ownProps.params.slug,
				window.VideoAppConfig.securityToken
			))
		}

	})
)(VideoDetail);