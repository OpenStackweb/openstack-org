import React from 'react';
import {connect} from 'react-redux';
import ButtonGroup from '../ui/ButtonGroup';
import ButtonOption from '../ui/ButtonOption';

export default({

}) => (
	<ButtonGroup>
		<ButtonOption>Yes</ButtonOption>
		<ButtonOption>Maybe</ButtonOption>
		<ButtonOption>Pass</ButtonOption>
	</ButtonGroup>
);