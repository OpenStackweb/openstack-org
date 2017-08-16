/**
 * Copyright 2017 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

import React from 'react';
import StorySearchForm from './components/containers/StorySearchForm';
import AllStories from './components/containers/AllStories';
import Message from "~core-components/message";
import { connect } from 'react-redux';
import { fetchAllStories, changeActiveView, changeActiveDist } from './actions';

class UserStoriesApp extends React.Component {

    constructor (props) {
        super(props);

        this.changeView = this.changeView.bind(this);
        this.changeDistribution = this.changeDistribution.bind(this);

    }

    changeView (e) {
        e.preventDefault();
        let view   = e.target.dataset.view;
        let fetch_stories = (this.props.active_view == 'search');

        this.props.changeActiveView(view, fetch_stories);
    }

    changeDistribution (e) {
        e.preventDefault();
        let dist   = e.target.dataset.distribution;

        this.props.changeActiveDist(dist);
    }

	render () {
		const {
            stories,
            loading,
            distribution,
            active_view,
            hasMore,
            views
            } = this.props;

		return (
		<div>
            <Message />
            <div className="container">
                <div className="user-stories-nav">
                    <div className="row">
                        <div className="col-sm-8">
                            <div className="row">
                                {views.filter(v => ( v.show )).map((v,i) => (
                                    <div key={i} className={"col-sm-2 nav-button-wrapper " + (active_view == v.view ? 'active' : '')}>
                                        <a
                                            onClick={this.changeView}
                                            className="nav-button"
                                            data-view={v.view}
                                        >
                                            { v.label }
                                        </a>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="col-sm-3">
                            <div className="search-wrapper">
                                <StorySearchForm />
                            </div>
                        </div>
                        <div className="col-sm-1">
                            <div className="distribution-wrapper">
                                <a
                                    className={"dist-tiles " + (distribution == 'tiles' ? 'active' : '')}
                                    onClick={this.changeDistribution}
                                >
                                    <i className="fa fa-th-large" data-distribution="tiles"></i>
                                </a>
                                <a
                                    className={"dist-list " + (distribution == 'list' ? 'active' : '')}
                                    onClick={this.changeDistribution}
                                >
                                    <i className="fa fa-th-list" data-distribution="list"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="story-page-main">
                    <div className="story-app-layout">
                        <AllStories views={views} />
                    </div>
                </div>
            </div>
		</div>
		);
	}
}

export default connect (
	state => {
		return {
			msg: state.msg,
            loading: state.loading,
            hasMore: state.has_more,
            stories: state.stories,
            distribution: state.distribution,
            active_view: state.active_view
		}
	},
	dispatch => ({
        changeActiveView(active_view, fetch_stories){
            return dispatch(changeActiveView(active_view, 'first', fetch_stories));
        },
        changeActiveDist(distribution){
            return dispatch(changeActiveDist(distribution));
        }
	})
)(UserStoriesApp);