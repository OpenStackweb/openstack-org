import React from 'react';
import URL from '../../utils/url';
import { connect } from 'react-redux';
import SpeakerItem from '../containers/SpeakerItem';
import GalleryPanel from '../ui/GalleryPanel';
import { fetchSpeakers } from '../../actions';
import FadeAnimation from '../ui/FadeAnimation';
import Loader from '../ui/Loader';
import AlphabetBar from '../ui/AlphabetBar';
import BlockButton from '../ui/BlockButton';
import RouterLink from '../containers/RouterLink';
import { routeActions } from 'react-router-redux';
import Helmet from 'react-helmet';

class Speakers extends React.Component {
	
	constructor (props) {
		super(props);
		this.loadMoreSpeakers = this.loadMoreSpeakers.bind(this);
	}

	componentDidMount () {
		if(!this.props.speakers.length) {
			this.props.fetchSpeakers(0, this.props.letter);
		}
	}

	componentWillReceiveProps (nextProps) {
		if(nextProps.letter !== this.props.letter) {
			this.props.fetchSpeakers(0, nextProps.letter);
		}
	}

	loadMoreSpeakers (e) {
		e.preventDefault();
		this.props.fetchSpeakers(
			this.props.speakers.length,
			this.props.letter
		);
	}

	createLetterLink (letter) {
		return URL.create('speakers', {letter});
	}

	render () {
		return (
			<div>
				<Helmet title="Speakers" />
				<div className="container">
					<div className="row">
						<div className="col-sm-12">
							<AlphabetBar 
								className='speaker-name-filter'
								label='Browse by last name'
								selected={this.props.letter}
								linkProvider={this.createLetterLink}
								onLetterClicked={this.props.goToLetter}
							/>
							<div className="popular-speakers-link">
								Or Browse By:
								<RouterLink link='speakers' active={!this.props.letter}>Popular Speakers</RouterLink>
							</div>						
						</div>
					</div>
					{() => {
						if(this.props.loading && !this.props.speakers.length) {
							return <Loader />
						}
						return (

							<div className="row">
								<div className="video-app-speaker-videos">
									<div className="col-sm-12">
										<h2>{this.props.letter ? this.props.letter.toUpperCase() : 'Popular speakers'}</h2>
									</div>
									<GalleryPanel className='video-panel'>
										<FadeAnimation>
											{this.props.speakers.map(speaker => (
												<SpeakerItem key={speaker.id} speaker={speaker} />
											))}
										</FadeAnimation>		
									</GalleryPanel>
								</div>
								{this.props.hasMore && !this.props.loading &&
									<BlockButton onButtonClicked={this.loadMoreSpeakers} className="more-btn">
										More speakers
									</BlockButton>
								}
								{this.props.loading && <Loader />}

							</div>
						);
					}()}
				</div>
			</div>
		);		
	}	
}

export default connect (
	state => {
		return {
			loading: state.speakers.loading,
			speakers: state.speakers.results,
			hasMore: state.speakers.has_more,
			total: state.speakers.total,
			letter: state.router.location.query.letter
		}
	},
	dispatch => ({
		fetchSpeakers (start = 0, letter) {
			const params = letter ? {start, letter} : {start};
			dispatch(fetchSpeakers(params));
		},

		goToLetter (letter) {
			dispatch(routeActions.push(
				URL.create('speakers', {letter})
			));
		}
	})
)(Speakers);