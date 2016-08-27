import React from 'react';
import http from 'superagent';

export default class extends React.Component {
	
	_load () {
		window.FB.XFBML.parse();
		gapi.plus.go();		
		twttr.widgets.load();
	}

	componentDidMount () {
		this._load();
	}

	
	componentDidUpdate (prevProps) {
		if(prevProps.presentationID !== this.props.presentationID) {
			this._load();
		}
	}

	
	render () {

		return (
		<div className="voting-share-wrapper">
			<h5>Share This Presentation</h5>
			<div className="sharing-section">
				<div className="single-voting-share">
					<a href="https://twitter.com/share" className="twitter-share-button" data-count="none">Tweet</a>
				</div>
				<div className="single-voting-share">
					<div className="g-plus" data-action="share" data-annotation="none"></div>
				</div>
				<div className="single-voting-share fb-share">
					<div className="fb-share-button" data-href="//developers.facebook.com/docs/plugins/" data-layout="button"></div>
				</div>
			</div>
		</div>
		);
	}
}