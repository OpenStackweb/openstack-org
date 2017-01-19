import React from 'react';
import Sidebar from './Sidebar';
import { Motion, spring, presets } from 'react-motion';
import animationStyle from '../../utils/animationStyle';
import { connect } from 'react-redux';
import { togglePresentationList } from '../../action-creators';

class MobileSidebar extends React.Component {

	render() {
		const { active, close } = this.props;
		const defaultStyle = {
			opacity: 0,			
			y: -window.innerHeight
		};
		const style = {
			y: spring(active ? 0 : -window.innerHeight),
			opacity: spring(active ? 1 : 0)
		};
		return (
			<Motion defaultStyle={defaultStyle} style={style}>
				{interpolatingStyle => {
					const s = animationStyle({
						panel: true,
						fixed: true,						
						zIndex: 100,
						...interpolatingStyle
					});
					
					return (
						<div style={s}>
							{active &&
							    <a onClick={close} className="voting-close-panel">
							   		&times;
							    </a>
							}
							<Sidebar filter={this.props.filter} />
						</div>
					);
				}}
			</Motion>
		);
	}
}

export default connect(
	state => ({
		active: state.mobile.showPresentationList
	}),
	dispatch => ({
		close(e) {
			e.preventDefault();
			dispatch(togglePresentationList());
		}
	})
)(MobileSidebar);
