import React from 'react';

class SimpleSearchForm extends React.Component {

	constructor (props) {
		super(props);
		this.handleSubmit = this.handleSubmit.bind(this);
	}

	handleSubmit (e) {
		if(this.props.onSearch) {
			e.preventDefault();
			this.props.onSearch(this.refs.search.value);
		}
	}
	render () {
		const {
			className,
			action,
			currentSearch,
			onSearchTyped,
			placeholder,
			buttonText
		} = this.props;

		return (
			<form method='GET' className={className} action={action} onSubmit={this.handleSubmit}>
				<input 
					placeholder={placeholder}
					type="text"
					name="search"
					ref="search"
					value={currentSearch}
					onChange={onSearchTyped} />
				<button type="submit">{buttonText}</button>
			</form>
		);
	}
}

SimpleSearchForm.propTypes = {
	actions: React.PropTypes.string,
	currentSearch: React.PropTypes.string,
	placeholder: React.PropTypes.string,
	buttonText: React.PropTypes.string,
	onSearchTyped: React.PropTypes.func,
	onSearch: React.PropTypes.func,	
};

SimpleSearchForm.defaultProps = {
	buttonText: 'Search',
	currentSearch: ''
};

export default SimpleSearchForm;