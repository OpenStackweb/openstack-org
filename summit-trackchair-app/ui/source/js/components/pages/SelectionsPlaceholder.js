import React from 'react';

class SelectionsPlaceholder extends React.Component {

	shouldComponentUpdate() {
		return false;
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

export default SelectionsPlaceholder;