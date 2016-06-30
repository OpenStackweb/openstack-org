import React from 'react';
import Tooltip from '../ui/Tooltip';
import 'rc-tooltip/assets/bootstrap.css';
import {Selected, Maybe, Pass} from '../ui/Icons';

export default ({
	presentation
}) => {
	let icons = {
		'selectors': Selected,
		'likers': Maybe,
		'passers': Pass
	};
	const tooltipLimit = 2;
	let arrowContent = <div className="rc-tooltip-arrow-inner"></div>;
	let innerContent = Object.keys(icons).map(k => {
		if(presentation[k].length) {
			let Icon = icons[k];
			let list = presentation[k];
			let listLength = list.length;
			if(list.length > tooltipLimit) {
				list = list.slice(0, tooltipLimit);
				list.push(`... and ${listLength - tooltipLimit} more`);
			}
			list = list.join('<br>');

			return (
				<Tooltip key={k} arrowContent={arrowContent} placement="bottom" overlay={list}>
    				<span className={`presentation-metric ${k}`}>
    					<Icon /> {presentation[k].length}
    				</span>
				</Tooltip>
			);
		}
	});
	innerContent.push(
		<span key='comments' className="presentation-metric comments">
			<i className="fa fa-comment" /> {presentation.comment_count}
		</span>
	);

	if(presentation.group_selected) {
		innerContent.push(
			<span key='team' className="presentation-metric team">
				<i className="fa fa-team" />
			</span>
		);
	}

	return (
		<span className="presentation-metrics">{innerContent}</span>
	);
}