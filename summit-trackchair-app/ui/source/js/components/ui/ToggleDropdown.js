import React from 'react';
import ReactDOM from 'react-dom';

class ToggleDropdown extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			open: false
		};

		this.handleClick = this.handleClick.bind(this);
	}

	handleClick(e) {
		this.setState({
			open: !this.state.open
		})
	}

	render () {
		const children = [];
		React.Children.forEach(this.props.children, (c,i) => {
			children.push(<li key={i}>{c}</li>);
			children.push(<li key={`${i}-divider`} className="divider" />);
		});

		return (
            <this.props.component className="dropdown">
                <a className="dropdown-toggle count-info" href="#" onClick={this.handleClick}>
                <i className={`fa fa-${this.props.icon}`}></i>  
                {this.props.badge &&
                	<span className="label label-warning">{this.props.badge}</span>
                }
                </a>
                <ul className="dropdown-menu dropdown-messages" style={{display: this.state.open ? 'block' : 'none'}}>
                	{children}
                </ul>
            </this.props.component>
        );

	}
}

ToggleDropdown.defaultProps = {
	component: 'LI',
	icon: 'bell'
};

export default ToggleDropdown;