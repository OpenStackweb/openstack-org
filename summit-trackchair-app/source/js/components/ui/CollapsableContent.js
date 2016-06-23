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
			return <div style={{opacity:0}}>{this.props.children}</div>
		}

		if(!this.state.needsCollapsing) {
			return <div>{this.props.children}</div>
		}

		const containerStyle = {
			position: 'relative',
			overflow: 'hidden'
		};

		const fadeStyle = {
			position: 'absolute',
			bottom: 0,
			left: 0,
			width: '100%',
			textAlign: 'center',
			margin: 0,
			padding: `${this.props.gradientHeight}px 0`
		};

		const buttonStyle = {
			textAlign: 'center'
		};

		if(this.state.collapsed) {
			fadeStyle.backgroundImage = `linear-gradient(to bottom, transparent, ${this.props.gradientColor})`;
			containerStyle.maxHeight = this.props.collapsedHeight;		
		}

		const buttonText = this.state.collapsed ? this.props.collapsedText : this.props.expandedText;

		return (
			<div>
				<div className={`collapsable-content ${this.state.collapsed ? 'collapsed' : 'expanded'}`} style={containerStyle}>
					{this.props.children}
					<div className="collapsable-content-fade" style={fadeStyle} />				
				</div>
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