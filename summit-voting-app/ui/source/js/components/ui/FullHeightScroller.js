import React from 'react';
import ReactDOM from 'react-dom';

class FullHeightScroller extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			height: 0
		}
		this.setSize = this.setSize.bind(this);
	}

	componentDidMount () {
		window.addEventListener('resize', this.setSize);		
		this.setSize();
	}

	componentWillUnmount () {
		window.removeEventListener('resize', this.setSize);
	}

	calculateSize () {
		const node = ReactDOM.findDOMNode(this);

		if(!node) return 0;
		
		const top = node.getBoundingClientRect().top;
		
		return window.innerHeight - top - this.props.pad;
	}

	setSize () {		
		this.setState({
			height: this.calculateSize()
		});
	}

	render () {
		const style = {
			overflowY: 'auto',
			height: this.state.height
		};

		return (
			<this.props.component {...this.props} style={style}>
				{this.props.children}
			</this.props.component>
		);
	}

}

FullHeightScroller.propTypes = {
	component: React.PropTypes.string,
	pad: React.PropTypes.number
};

FullHeightScroller.defaultProps = {
	component: 'div',
	pad: 0
};

export default FullHeightScroller;