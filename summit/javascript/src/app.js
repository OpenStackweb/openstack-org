/**
 * @jsx React.DOM
 */
var React = require('react');
var Router = require('react-router');
var Backend = require('./store/Backend');
var API = require('./api/api');

module.exports = React.createClass({displayName: 'VotingApp',
  

  componentWillMount: function () {
    this._registerErrorHandlers();
    Backend.getSummitData('active');
  },


  _registerErrorHandlers: function () {
    API.registerErrorHandler('404', function (res) {
      alert("Whoa, that's a 404");
    });

    API.registerErrorHandler('403', function (res) {
      alert("You sure you got access, bro?");
    })

    API.registerErrorHandler('TIMEOUT', function (res) {
      alert("That's a timeout.");
    });

    API.registerErrorHandler('ERROR', function(res){
      alert("Sum-ting-wong");
    });
  },

  render: function() {
    return (
      <div>
        <a href="#" className="voting-open-panel">
          <i className="fa fa-bars fa-2x collapse-voting-nav"></i>
        </a>
        <div className="container">
          <div className="row">
            <div className="voting-header">
              <div className="col-lg-3 col-md-3 col-sm-3">
                <div className="voting-app-logo">
                    <img ref="logo" className="summit-hero-logo" src="summit/images/voting-logo.svg" onError={this._handleLogoError} alt="OpenStack Summit" />                
                </div>
              </div>
              <div className="col-lg-6 col-md-6 col-sm-6">
                <div className="voting-app-title">
                  <h1>
                    Vote For Presentations
                    <span className="subheading">
                      Help us pick the presentations for The Vancouver Summit
                    </span>
                  </h1>
                </div>
              </div>
              <div className="col-lg-3 col-md-3 col-sm-3">
                <div className="voting-app-details">
                  <a href="/summit/" className="btn">
                    Summit Details
                  </a>
                </div>
              </div>
            </div>
          </div>              
          <div className="row" id="app">
            <Router.RouteHandler store={this.props.store} />
          </div>
        </div>
      </div>
    );
  },


  _handleLogoError: function () {
    this.refs.logo.getDOMNode().src = 'summit/images/voting-logo.png';
  }

});