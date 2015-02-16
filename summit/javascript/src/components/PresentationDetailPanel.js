/**
 * @jsx React.DOM
 */

var React = require('react');
var BS = require('react-bootstrap');
var FullHeightScroller = require('./FullHeightScroller');
var VotingBar = require('./VotingBar');


var Col = BS.Col;
var Row = BS.Row;

module.exports = React.createClass({

	handleVote: function (val) {
		this.props.onVote(val);
	},

	render: function () {
		var voteOptions = [
			{value: 3, label: "I would love to see this! [4]"},
			{value: 2, label: "I'd try to see this [3]"},
			{value: 1, label: "I might see this [2]"},
			{value: -1, label: "I would not see this [1]"},
			{value: 0, label: "No opinion [0]"}
		];

		return (
			<Col xs={12} md={8}>
				<Row>
					<Col xs={12} md={12}>
						<small>TITLE</small>
						<h3>{this.props.presentation.title}</h3>
					</Col>
				</Row>
				<Row>
					<Col xs={12} md={12}>
						<FullHeightScroller>
							<Row>
								{this.props.presentation.speakers.map(function(speaker) {
									return (
									<Col xs={12} md={4} key={speaker.id} className="speaker-preview">
										<img src={speaker.photo_url} width={30} />
										<div><strong>{speaker.first_name} {speaker.last_name}</strong></div>
										<div>{speaker.title}</div>
									</Col>
									);
								})}								
							</Row>
							<Row>
								<Col xs={12} md={12}>
									<small>ABSTRACT</small>
									<div dangerouslySetInnerHTML={{__html: this.props.presentation.description}} />
								</Col>
							</Row>
							<Row>
								{this.props.presentation.speakers.map(function(speaker) {
									return (
									<Col key={speaker.id} xs={12} md={12}>
										<img src={speaker.photo_url} />
										<h3>{speaker.first_name} {speaker.last_name}</h3>
										<h4>{speaker.title}</h4>
										<div dangerouslySetInnerHTML={{__html: speaker.bio}} />
									</Col>
									);
								})}
							</Row>
							<div className="voting-bar">
								<VotingBar								
									value={parseInt(this.props.presentation.user_vote)}
									options={voteOptions}
									onChange={this.handleVote} />
							</div>
						</FullHeightScroller>
					</Col>
				</Row>
			</Col>
		);

	}
});
