/**
 * @jsx React.DOM
 */
// Stylesheet
require('../../css/style.less');

var React = require('react');
var PresentationApp = require('./app');
var PresentationBrowse = require('./pages/PresentationBrowse');
var Router = require('react-router');
var Route = Router.Route;
var Routes = Router.Routes;
var DefaultRoute = Router.DefaultRoute;


var routes = (
	<Routes>
	    <Route name="app" path="/" handler={PresentationApp}>	      
	      <Route name="browse" handler={PresentationBrowse} />
	      <Route name="detail" path="/browse/:presentationid" handler={PresentationBrowse} />
	    </Route>
	</Routes>
);
React.renderComponent(routes, document.body);