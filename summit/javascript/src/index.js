/**
 * @jsx React.DOM
 */
var React = require('react');
var VotingApp = require('./app');
var Main = require('./pages/Main');
var Detail = require('./pages/Detail');
var DefaultDetail = require('./pages/DefaultDetail');
var Router = require('react-router');
var Route = Router.Route;
var Routes = Router.Routes;
var DefaultRoute = Router.DefaultRoute;
var Cortex = require('./store/Cortex');

var routes = (	
	<Route path="/summit/tokyo-2015/call-for-speakers" handler={VotingApp}>	    
	      <Route name="home" path="vote" handler={Main}>
	      	<DefaultRoute handler={DefaultDetail} />
	      	<Route name="detail" path="show/:presentationID" handler={Detail} />    
	      </Route>
    </Route>
);


var RootComponent;
Router.run(routes, Router.HistoryLocation, function (Handler) {
  RootComponent = React.render(<Handler store={Cortex} />, document.getElementById('wrap'));
});

Cortex.on('update', function (updatedData) {
	RootComponent.setProps({
		store: updatedData
	});
});