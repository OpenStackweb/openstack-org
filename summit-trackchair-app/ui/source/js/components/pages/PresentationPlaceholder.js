import React from 'react';

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

class PresentationPlaceholder extends React.Component {

	shouldComponentUpdate() {
		return false;
	}

	getLines() {
    	let lines = [], i = 0, max = 5;
    	while(i < max) {
    		lines.push(<span key={i} className="content-line" style={{width: `${getRandomInt(25,100)}%`}}/>);
    		i++
    	}
    	return lines;
	}

	render() {
		return (
		<div className="wrapper wrapper-content presentation-placeholder">
		   <div className="ibox">
		      <div className="ibox-content">
		         <div className="row">
		            <div className="col-lg-12">
		            	
		            </div>
		         </div>
		      </div>
		   </div>
		</div>
		);
	}
}

export default PresentationPlaceholder;