import React from 'react';

const TopBanner = ({
	children,
	className,
	onDismiss,
	dismissText
}) => (
<div className={`notification-banner ${className}`}>
    <div className="container">
        <p>
            <span>{children}</span>
            <a onClick={onDismiss} className="notification-banner-button">{dismissText}</a>
        </p>
    </div>
</div>
);

TopBanner.propTypes = {
	onDismiss: React.PropTypes.func,
	dismissText: React.PropTypes.string
};

TopBanner.defaultProps = {
	dismissText: 'Dismiss'
};

export default TopBanner;