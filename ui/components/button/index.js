import React from 'react';
import buttonStyles from './button.module.scss';
import cx from 'classnames';

export default ({
	active,
	disabled,
	children,
	onButtonClicked
}) => {

	return (
		<button onClick={onButtonClicked} className={cx({
			[buttonStyles.button]: true,	
			[buttonStyles.disabled]: disabled,
			[buttonStyles.active]: active
		})}>{children}</button>
	);
}

