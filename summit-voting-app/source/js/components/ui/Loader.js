import React from 'react';
import cx from 'classnames';
const Loader = ({
	type,
	className,
	active
}) => {
	if(!active) return <div />;

	switch (type) {
		case 'bounce':
			return (
				<div className={cx(["spinner bounce-loader", className])}>
				  <div className="bounce1"></div>
				  <div className="bounce2"></div>
				  <div className="bounce3"></div>
				</div>
			);

		case 'wave':
			return (
				<div className={cx(["spinner wave-loader", className])}>
				  <div className="rect1"></div>
				  <div className="rect2"></div>
				  <div className="rect3"></div>
				  <div className="rect4"></div>
				  <div className="rect5"></div>
				</div>		
			);
		case 'spin':
			return <div className={cx(['spin-loader', className])} />;
	}
}

Loader.propTypes = {
	type: React.PropTypes.oneOf(['bounce', 'wave','spin']).isRequired,
	delay: React.PropTypes.number,
	active: React.PropTypes.bool
};

Loader.defaultProps = {
	type: 'bounce',
	active: false
};

export default Loader;