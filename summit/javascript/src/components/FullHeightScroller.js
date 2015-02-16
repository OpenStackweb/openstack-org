/**
 * @jsx React.DOM
 */
var React = require('react');

module.exports = React.createClass({
    
    sizeListener: function () {
      var el = this.getDOMNode();
      var rect = el.getBoundingClientRect();
      var top = rect.top + document.body.scrollTop;

      el.style.height = (window.innerHeight - top);
      el.style.overflowY = 'auto';
    },


    scrollListener: function () {
      var d = this.getDOMNode();
      this.props.scrollHandler(
        d.scrollTop,
        d.scrollHeight,
        d.clientHeight
      );
    },


    attachSizeListener: function () {      
      window.addEventListener('resize', this.sizeListener);
      this.sizeListener();

    },


    detachSizeListener: function () {      
      window.removeEventListener('resize', this.sizeListener);
    },


    attachScrollListener: function () {      
      this.getDOMNode().addEventListener('scroll', this.scrollListener);
      this.scrollListener();

    },


    detachScrollListener: function () {      
      this.getDOMNode().removeEventListener('resize', this.scrollListener);
    },


    componentWillUnmount: function () {
      this.detachSizeListener();

      if(this.props.scrollHandler) {
        this.detachScrollListener();
      }
    },


    componentDidMount: function () {
    	this.attachSizeListener();

      if(this.props.scrollHandler) {
        this.attachScrollListener();
      }
    },

    
    render: function () {
    	return this.transferPropsTo(
    		<div>{this.props.children}</div>
    	);
    }

});