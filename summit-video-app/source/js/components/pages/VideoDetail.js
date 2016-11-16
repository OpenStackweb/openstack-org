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
		}
	}

	componentWillReceiveProps (nextProps) {
		if(!this.props.loading) {
			if(!this.props.video || (nextProps.video && nextProps.video.slug !== nextProps.params.slug)) {				
				this.props.fetchVideoDetail(nextProps.params.slug);
			}
		}
	}

	render () {
		const {video} = this.props;
		if(!video) {
			return <Loader />;
		}

        // change og meta tags for sharing
        $('meta[property="og:title"]').attr('content', video.title);
        $('meta[property="og:url"]').attr('content', window.location.href );
        $('meta[property="og:image"]').attr('content', video.thumbnailURL);
        $('meta[property="og:description"]').attr('content', video.description);
        $('meta[name="twitter:title"]').attr('content', video.title);
        $('meta[name="twitter:image"]').attr('content', video.thumbnailURL);
        $('meta[name="twitter:description"]').attr('content', video.description);
        $('link[rel="image_src"]').attr('href', video.thumbnailURL);

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
								{video.speakers && video.speakers.length > 0 &&
                                <div className="detail-panel-section">
                                    <h5 className="section-title">Speakers</h5>
									<ul className="video-speakers-list">
									{video.speakers.map(s => (
										<li key={s.id}><RouterLink link={`speakers/${s.id}/${s.name_slug}`}>{s.name}</RouterLink></li>
									))}
									</ul>
								</div>
                                }
                                {video.summit && video.summit.title &&
                                    <div className="detail-panel-section">
                                        <h5 className="section-title">Summit</h5>
                                        <RouterLink link={`summits/${video.summit.slug}`}>
                                            {video.summit.title}
                                        </RouterLink>
                                    </div>
                                }
                                {video.track && video.track.title &&
                                <div className="detail-panel-section">
                                    <h5 className="section-title">Track</h5>
                                    <RouterLink link={`${video.summit.slug}/tracks/${video.track.slug}`}>
                                        {video.track.title}
                                    </RouterLink>
                                </div>
                                }
                                {video.tags.length > 0 &&
                                <div className="detail-panel-section">
                                    <h5 className="section-title">Tags</h5>
                                    {video.tags && video.tags.map(tag => (
                                        <RouterLink key={tag.id} className="tag btn btn-primary btn-xs" link={`tags/${tag.tag}`}>
                                            {tag.tag}
                                        </RouterLink>
                                    ))}
                                </div>
                                }
								<div className="detail-panel-section">		
									<h5 className="section-title">Views</h5>									      		
									{Number(video.views).toLocaleString()}		
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
		}

	})
)(VideoDetail);