/**
 * @jsx React.DOM
 */
var React = require('react');
var BS = require('react-bootstrap');

module.exports = React.createClass({

	render: function () {
		return (
			    <BS.Navbar>
			      <BS.Nav>
			        <BS.NavItem key={1} href="#">Browse Presentations</BS.NavItem>
			        <BS.NavItem key={2} href="#">Team Selections</BS.NavItem>
			        <BS.NavItem key={3} href="#">Chair Directory</BS.NavItem>
			        <BS.NavItem key={4} href="#">Quick Tutorial</BS.NavItem>
			      </BS.Nav>
			    </BS.Navbar>			
		);
	}
});
