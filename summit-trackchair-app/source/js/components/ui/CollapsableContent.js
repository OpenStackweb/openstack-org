import React from 'react';
import ReactDOM from 'react-dom';

class CollapsableContent extends React.Component {

	constructor (props) {
		super(props);
		this.state = {
			collapsed: true,
			needsCollapsing: false,
			ready: false
		}
		this.toggle = this.toggle.bind(this);
	}

	toggle(e) {
		e.preventDefault();
		this.setState({
			collapsed: !this.state.collapsed
		});
	}

	componentDidMount() {
		const node = ReactDOM.findDOMNode(this);
		if(node) {
			this.setState({
				ready: true,
				needsCollapsing: (node.clientHeight > this.props.collapsedHeight + this.props.gradientHeight)
			});
		}
	}

	render () {

		if(!this.state.ready) {
			return <div style={{visibility:'hidden'}}>{this.props.children}</div>
		}

		if(!this.state.needsCollapsing) {
			return <div>{this.props.children}</div>
		}

		const containerStyle = {
			position: 'relative',
			overflow: 'hidden'
		};

		const buttonStyle = {
			position: 'absolute',
			bottom: 0,
			left: 0,
			width: '100%',
			textAlign: 'center',
			margin: 0,
			padding: `${this.props.gradientHeight}px 0`
		};

		if(this.state.collapsed) {
			buttonStyle.backgroundImage = `linear-gradient(to bottom, transparent, ${this.props.gradientColor})`;
			containerStyle.maxHeight = this.props.collapsedHeight;		
		}

		const buttonText = this.state.collapsed ? this.props.collapsedText : this.props.expandedText;

		return (
			<div className={`collapsable-content ${this.state.collapsed ? 'collapsed' : 'expanded'}`} style={containerStyle}>
				{this.props.children}
				<div className="collapsable-content-button" style={buttonStyle}>
					<a onClick={this.toggle} href="#">{buttonText}</a>
				</div>
			</div>
		);
	}
}

CollapsableContent.defaultProps = {
	collapsedText: 'More...',
	expandedText: 'Less',
	collapsedHeight: 200,
	gradientColor: '#eee',
	gradientHeight: 20

};

export default CollapsableContent;