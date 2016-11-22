import React from 'react';
import createAutoLink from '../../utils/autoLink';

class FeatureImagePanel extends React.Component {

	constructor (props) {
		super(props);
		this.handleLinkClicked = this.handleLinkClicked.bind(this);
	}

	handleLinkClicked (e) {
		if(this.props.onLinkClicked) {
			e.preventDefault();
			this.props.onLinkClicked(this.props.link);
		}
	}

	render () {
		const {
			className,
			imageUrl,
			title,
			subtitle,
			link
		} = this.props;

		const autoLink = createAutoLink(link, this.handleLinkClicked);

		return (
			<div className={className}>
				{autoLink(
				<div>
					<div className="feature-image">
						<img src={imageUrl} />
					</div>
					<div className="feature-meta">
						<h5>{title}</h5>
						<div className='gallery-subtitle'>
							{subtitle}
						</div>
					</div>
				</div>
				,{className: 'panel-link'})}
			</div>

		);
	}
}

FeatureImagePanel.PropTypes = {
	imageUrl: React.PropTypes.string,
	title: React.PropTypes.string,
	subtitle: React.PropTypes.string,
	link: React.PropTypes.string,
	onLinkClicked: React.PropTypes.func
};

export default FeatureImagePanel;