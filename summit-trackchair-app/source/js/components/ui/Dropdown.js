import React from 'react';
import ReactDOM from 'react-dom';
import nodeInRoot from '../../utils/nodeInRoot';
import cx from 'classnames';

class Dropdown extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			opened: false
		};

		this.closeOnOuterClick = this.closeOnOuterClick.bind(this);
	}


	componentDidMount () {
		document.addEventListener('click', this.closeOnOuterClick);
	}


	componentWillUnmount () {
		document.removeEventListener('click', this.closeOnOuterClick);
	}


	closeOnOuterClick (e) {
		if(!nodeInRoot(e.target, ReactDOM.findDOMNode(this))) {
			this.setState({opened: false});
		}
	}


	render () {
		const { children } = this.props;
		return (
		    <div className={cx(['btn-group', this.props.className])}>
				<button type="button" className="btn btn-default dropdown-toggle" onClick={(e) => {
					this.setState({opened: !this.state.opened})
				}}>
					{this.props.selectedText}&nbsp;
					{this.props.caret &&
						<span className="caret" />
					}
				</button>				
				<ul style={{display: this.state.opened ? 'block' : 'none'}} className="dropdown-menu" role="menu">
					{children && React.Children.map(children, (child, i) => {						
						return (
							child &&
							React.cloneElement(
								child,
								{									
									key: i,
									onItemClick: (e) => {
										e.preventDefault();
										if(child.props.divider) return;
										
										this.setState({opened: false});
										this.props.onItemSelected && this.props.onItemSelected(child.props.eventKey);
									},
									active: this.props.activeKey === child.props.eventKey
								}
							)
						);
					})}
				</ul>
			
		    </div>
	   	);

	}
}

Dropdown.propTypes = {
	onItemSelected: React.PropTypes.func,
	selectedText: React.PropTypes.any,
	activeKey: React.PropTypes.any,
	caret: React.PropTypes.bool
};

Dropdown.defaultProps = {
	caret: true
}

export default Dropdown;