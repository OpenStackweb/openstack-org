import React from 'react';

const ShareButton = ({
	service,
	url,
	windowTitle,
	windowWidth,
	windowHeight
}) => {
	let endpoint;
	url = encodeURI(url || window.location.href);

	switch(service) {
		case 'facebook':
			endpoint = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
			break;
		case 'twitter':
			endpoint = `http://www.twitter.com/share?url=${url}`;
			break;
		case 'linkedin':
			endpoint = `https://www.linkedin.com/shareArticle?mini=true&url=${url}`;
			break;
		case 'google-plus':
			endpoint = `https://plus.google.com/share?url=${url}`;
			break;
	}

	return (
		<a href="javascript:void(0);" onClick={(e) => {
			e.preventDefault();
			window.open(
				endpoint, 
				windowTitle,
				`menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,width=${windowWidth},height=${windowHeight}`
			);
		}}>
			<i className={`fa fa-${service}-square`}></i>
		</a>
	);

};

ShareButton.defaultProps = {
	windowTitle: 'Share',
	windowHeight: 600,
	windowWidth: 600
};

ShareButton.propTypes = {
	service: React.PropTypes.oneOf([
		'facebook', 'twitter', 'google-plus', 'linkedin'
	])
};

export default ShareButton