import React from 'react';
import BaseTooltip from 'rc-tooltip';

class Tooltip extends BaseTooltip {
  getPopupElement() {
    var _props = this.props;
    var arrowContent = _props.arrowContent;
    var overlay = _props.overlay;
    var prefixCls = _props.prefixCls;

    return (
    	<div> 
    		<div className={`${prefixCls}-arrow`} key='arrow' />
    		<div className={`${prefixCls}-inner`} dangerouslySetInnerHTML={{__html: overlay}} key='content' />
    	</div>

    );
  }

}

export default Tooltip;