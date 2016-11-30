export const q = (state, q) => (
	state.routing.locationBeforeTransitions.query[q]
);

export const path = state => state.routing.locationBeforeTransitions.pathname;

export const isPath = pathname => state => (
	path(state).match(new RegExp(pathname))
);
