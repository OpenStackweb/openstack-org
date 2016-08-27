import React from 'react';

class AutoCompleteInput extends React.Component {

	constructor(props) {
		super(props);

	}
	render() {
		const {children, onChange, placeholder, value, className, disabled} = this.props;

		return (
			<div className="autocomplete-input">
				<input onChange={onChange} placeholder={placeholder} value={value} className={className} disabled={disabled} />
				{children &&
					<ul className="autocomplete-results">
						{children}
					</ul>
				}
			</div>
		);
	}
}

export default AutoCompleteInput;