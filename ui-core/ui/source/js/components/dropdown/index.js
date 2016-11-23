import React from 'react';
import dropdownStyles from './dropdown.scss';

export default class extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			opened: false
		};
		this.toggle = this.toggle.bind(this);
		this.handleSelect = this.handleSelect.bind(this);
	}

	toggle(e) {
		this.setState({
			opened: !this.state.opened
		});
	}

	handleSelect(e, key) {
		alert(`you selected ${key}`);
	}

	render() {
		return (
			<div>
				<h3 onClick={this.toggle}>{this.props.title}</h3>
				{this.state.opened &&
					<ul>
						{this.props.options.map(option => (
							<li key={option.key} onClick={this.handleSelect.bind(null, option.key)}>
								{option.text}
							</li>
						))}
					</ul>
				}
			</div>
		);
	}
}
