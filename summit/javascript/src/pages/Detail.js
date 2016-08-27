var React = require('react');
var Backend = require('../store/Backend');
var Router = require('react-router');
var HotkeyBar = require('../components/HotkeyBar');
var HotkeyButton = require('../components/HotkeyButton');
var share = require('../utils/share');

var Detail = React.createClass({displayName: 'Detail',

	mixins: [Router.State],

	
	_presentation: null,

	
	componentWillReceiveProps: function(nextProps) {		
		this._selectPresentation();
	},

	componentWillMount: function() {
		this._selectPresentation();
	},


	_selectPresentation: function () {
		var presID = this.getParams().presentationID;
		var store = this.props.store;

		if(!store.activePresentation.getValue() || presID != store.activePresentation.id.getValue()) {			
			Backend.viewPresentation(presID);
			Backend.setPresentationActive(presID);
		}
	},


	_handleVote: function (vote) {		
		Backend.setPresentationVote(
			this.props.store.activePresentation.id.getValue(),
			vote
		);
	},

	render: function() {
		var pres;
		var votingBar;

		if(!this.props.store.activePresentation.getValue()) {
			return <div className="loading">Loading...</div>
		}

		pres = this.props.store.activePresentation.getValue();

		console.log("pres object", pres);

		votingBar = (
            <HotkeyBar onChange={this._handleVote} selection={pres.user_vote}>
            	<HotkeyButton hotkeys={[51,99]} hotkeyDescription={3} val={3}>Would Love to See!</HotkeyButton>
            	<HotkeyButton hotkeys={[50,98]} hotkeyDescription={2} val={2}>Would Try to See</HotkeyButton>
            	<HotkeyButton hotkeys={[49,97]} hotkeyDescription={1} val={1}>Might See</HotkeyButton>
            	<HotkeyButton hotkeys={[48,96]} hotkeyDescription={0} val={0}>Would Not See</HotkeyButton>
            </HotkeyBar>
		);

		return (			
		<div>
          <a href="#" className="voting-open-panel text">
            <i className="fa fa-chevron-left" />
            All Submissions
          </a>
          <div className="voting-content-body">
            {pres.can_vote && 
            	<div>
		            <h5>Cast Your Vote</h5>
		            {votingBar}
	            </div>
        	}
        	{!pres.can_vote &&
        		<div style={{clear:'both'}} className="vote-login">
        			Think this presentation should be included in the Barcelona Summit? Login to vote.<br/>
        			<a className="btn btn-default" href={ "/Security/login?BackURL=/vote-for-speakers/show/" + pres.id }>I already have an account</a>  <a href={ "/summit-login/login?BackURL=/vote-for-speakers/show/" + pres.id } className="btn btn-default">Sign up now</a>
        		</div>
        	}
            <div className="voting-presentation-title">
              <h5>Title</h5>
              <h3>{pres.title}</h3>
            </div>
            <div className="voting-presentation-body">
              <h5>Speakers</h5>
              <div className="voting-speaker-row">
                <ul>
                {pres.speakers && pres.speakers.map(function (speaker) {
                  return (
	                  <li key={speaker.id}>
	                    <img className="voting-speaker-pic" src={speaker.photo_url} />
	                    <div className="voting-speaker-name">
	                      {speaker.first_name + " " + speaker.last_name}
	                      <span>
	                        {speaker.title}
	                      </span>
	                    </div>
	                  </li>
                  );
                })}
                </ul>
              </div>
              <div className="voting-abstract">
              	<h5>Abstract</h5>              
              	<div dangerouslySetInnerHTML={{__html: pres.short_description}} />
              </div>
              <div className="main-speaker-wrapper">
              {pres.speakers && pres.speakers.map(function (speaker) {
              	return (
              		<div key={speaker.id}>
		                <div className="main-speaker-row">
		                  <div className="voting-speaker-name">
		                    {speaker.first_name + " " + speaker.last_name}
		                    <span>
		                      {speaker.title}
		                    </span>
		                  </div>
		                  <img className="voting-speaker-pic" src={speaker.photo_url} />
		                </div>
		                <div className="main-speaker-description">
		                  <div dangerouslySetInnerHTML={{__html: speaker.bio}} />
		                </div>
	                </div>
	            );
              })}
              </div>
            </div>
            {pres.can_vote && 
            	<div>
		            <h5>Cast Your Vote</h5>
		            {votingBar}
	            </div>
        	}
        	{!pres.can_vote &&
        		<div style={{clear:'both'}} className="vote-login">
        			Think this presentation should be included in the Barcelona Summit? Login to vote.<br/>
        			<a className="btn btn-default" href={ "/Security/login?BackURL=/vote-for-speakers/show/" + pres.id }>I already have an account</a>  <a href={ "/summit-login/login?BackURL=/vote-for-speakers/show/" + pres.id } className="btn btn-default">Sign up now</a>
        		</div>
        	}
            <div className="voting-share-wrapper">
              <h5>
                Share This Presentation
              </h5>
              	<a className="btn btn-default" href={ "https://www.facebook.com/sharer/sharer.php?u=http://www.openstack.org/vote-for-speakers/show/" + pres.id }>facebook</a> &nbsp;
              	<a className="btn btn-default" href={ "https://twitter.com/intent/tweet?&url=http://www.openstack.org/vote-for-speakers/show/" + pres.id }>Twitter</a> &nbsp;
              	<a className="btn btn-default" href={ "https://www.linkedin.com/cws/share?url=http://www.openstack.org/vote-for-speakers/show/" + pres.id }>LinkedIn</a> &nbsp;

             </div>
          </div>
        </div>
		);
	}

});

module.exports = Detail;
