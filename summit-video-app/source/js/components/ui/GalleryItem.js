import React from 'react';
import createAutoLink from '../../utils/autoLink';

class GalleryItem extends React.Component {

	constructor (props) {
		super(props);

		this.handleClick = this.handleClick.bind(this);
	}

	handleClick (e) {
		if(this.props.onItemClicked) {		
			e.preventDefault();
			return this.props.onItemClicked(this.props.link);
		}
	}

	render () {
		let {
			className,
			imageUrl,
			imageCaption,
			title,
			subtitle,
			link,
			badge
		} = this.props;

		const autoLink = createAutoLink(link, this.handleClick);

		return (
			<div className="col-xs-6 col-sm-3">
				{autoLink(
					<div className={className}>					
						<div className="gallery-image">
							<div className="gallery-image-caption">
								{imageCaption}
							</div>
							<img src={imageUrl} />
						</div>					
						<div className="gallery-title">
							{title}
						</div>
						<div className='gallery-subtitle'>
							{subtitle}
						</div>
						{badge && 
							<div className='gallery-badge'>{badge}</div>
						}
					</div>
				,{className: 'panel-link'})}
			</div> 
		);
	}
}

GalleryItem.propTypes = {
	imageUrl: React.PropTypes.string,
	imageCaption: React.PropTypes.string,
	title: React.PropTypes.string,
	subtitle: React.PropTypes.string,
	link: React.PropTypes.string,
	onItemClicked: React.PropTypes.func,
	badge: React.PropTypes.any
};

export default GalleryItem;