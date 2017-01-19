export default (animation = {}) => {
	const style = {...animation};
	if(style.fixed) {
		style.position = 'fixed';
	}
	if(typeof style.x !== 'undefined' || typeof style.y !== 'undefined' || typeof style.z !== 'undefined') {		
		style.position = style.fixed ? 'fixed' : 'absolute';
		const x = style.x || 0;
		const y = style.y || 0;
		const z = style.z || 0;
		
		style.WebkitTransform = `translate3d(${x}px,${y}px,${z}px)`;
		style.transform = `translate3d(${x}px,${y}px,${z}px)`;
		delete style.x;
		delete style.y;
		delete style.z;
		delete style.fixed;
	}
	['scaleX','scaleY','scale'].forEach(k => {
		if(typeof style[k] !== 'undefined') {
			style.WebkitTransform = `${k}(${style[k]})`;
			style.transform = `${k}(${style[k]})`;
			delete style[k];
		} 
	});

	if(style.panel) {
		style.top = 0;
		style.left = 0;
		style.bottom = 0;
		style.right = 0;
		delete style.panel;
	}

	return style;
}
