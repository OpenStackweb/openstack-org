export default (children = [], compareProp) => {
	const collatedChildren = [];
	let childSet = [], next;
	
	children.forEach((child, i) => {
		childSet.push(child);
		next = i+1;
		if(!children[next] || children[next][compareProp] !== child[compareProp]) {
			collatedChildren.push(childSet);
			childSet = [];			
		}
	});

	return collatedChildren;
};
