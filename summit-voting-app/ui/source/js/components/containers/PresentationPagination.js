import React from 'react';
import { connect } from 'react-redux';
import PaginationButton from '../ui/PaginationButton';
import { adjacentPresentation } from '../../action-creators';

const PresentationPagination = ({
	hasNext,
	hasPrev,
	goNext,
	goPrev
}) => {
	const next = hasNext ?
		<PaginationButton onPaginate={goNext} direction='next' /> :
		<PaginationButton disabled={true} direction='next' />;
	const prev = hasPrev ?
		<PaginationButton onPaginate={goPrev} direction='prev' /> :
		<PaginationButton disabled={true} direction='prev' />;

	return (
		<div className="presentation-pagination">
			{next}
			{prev}
		</div>
	);
};

export default connect(
	state => ({
		hasNext: state.presentations.hasNext,
		hasPrev: state.presentations.hasPrev
	}),

	dispatch => ({
		goNext(e) {
			e.preventDefault();			
			dispatch(adjacentPresentation(1));
		},

		goPrev(e) {
			e.preventDefault();
			dispatch(adjacentPresentation(-1));
		}
	})
)(PresentationPagination);