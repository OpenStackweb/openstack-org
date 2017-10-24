/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

import React from 'react';
import PropTypes from 'prop-types';

class SimpleSearchForm extends React.Component {

	constructor (props) {
		super(props);

        this.state = { search_value: '' };

		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleChange = this.handleChange.bind(this);
	}

	handleSubmit (e) {
		if(this.props.onSearch) {
			e.preventDefault();
			this.props.onSearch(this.state.search_value);
		}
	}

    handleChange (e) {
        this.setState({search_value: e.target.value})
        this.props.onSearchTyped(e);
    }

	render () {
		const {
			className,
			action,
			currentSearch,
			placeholder,
			buttonText
		} = this.props;

		return (
			<form method='GET' className={className} action={action} onSubmit={this.handleSubmit}>
				<input 
					placeholder={placeholder}
					type="text"
					name="search"
					value={currentSearch}
					onChange={this.handleChange} />
				<button type="submit">{buttonText}</button>
			</form>
		);
	}
}

SimpleSearchForm.propTypes = {
	actions: PropTypes.string,
	currentSearch: PropTypes.string,
	placeholder: PropTypes.string,
	buttonText: PropTypes.oneOfType([
        PropTypes.string,
        PropTypes.object,
    ]),
	onSearchTyped: PropTypes.func,
	onSearch: PropTypes.func,
};

SimpleSearchForm.defaultProps = {
	buttonText: 'Search',
	currentSearch: ''
};

export default SimpleSearchForm;