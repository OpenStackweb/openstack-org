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
import { connect } from 'react-redux';
import StoryItem from './StoryItem';
import BlockButton from '../ui/BlockButton';
import { AjaxLoader } from "~core-components/ajaxloader";
import StoryGroupedView from '../views/StoryGroupedView';
import StoryUnGroupedView from '../views/StoryUnGroupedView';
import { fetchAllStories, loadStories, setUrlParams } from '../../actions';

class AllStories extends React.Component {
	
	constructor (props) {
		super(props);

		this.loadMoreStories = this.loadMoreStories.bind(this);
        this.handleScroll = this.handleScroll.bind(this);
	}

	componentDidMount () {
		if(!this.props.stories.length) {
            this.props.loadStories();
		}
        window.addEventListener('scroll', this.handleScroll);
	}

	componentWillUnmount() {
		window.removeEventListener('scroll', this.handleScroll);
	}

	componentDidUpdate(prevProps, prevState) {
        // if is first load and is tab location or industry
        if (this.props.section && this.props.section != 'first' && this.props.stories.length > 0) {
            $('html, body').animate({
                scrollTop: $("#"+this.props.section).offset().top - 30
            }, 2000);
        }
    }

	loadMoreStories (e) {
		e.preventDefault();
		this.props.fetchStories(
			this.props.stories.length
		)
	}

    handleScroll(e) {
        let props = this.props;
        $('.story-panel.grouped').each(function(){
            if (
                $(this).offset().top < window.pageYOffset + 70
                && $(this).offset().top + $(this).height() > window.pageYOffset + 70
            ) {
                props.setUrlParams({[props.active_view]: $(this).attr('id')})
            }
        });
    }

	render () {

        const {
            stories,
            loading,
            distribution,
            active_view,
            hasMore
        } = this.props;


		if(loading) {
			return <AjaxLoader show={ loading } relative={ true } color='white' size={ 75 } />
		}

		return (
			<div>
				{this.props.views.filter(v => ( v.grouped )).map((v,i) => v.view).find(x => x == active_view) &&
                    <StoryGroupedView stories={ stories } distribution={ distribution } group_by={ active_view } />
                }
                {this.props.views.filter(v => ( !v.grouped )).map((v,i) => v.view).find(x => x == active_view) &&
                    <StoryUnGroupedView stories={ stories } distribution={ distribution } group_by={ active_view } />
                }
				{hasMore && !loading &&
					<BlockButton onButtonClicked={this.loadMoreStories} className="more-btn">
						More stories
					</BlockButton>
				}
			</div>
		);
	}
}

export default connect (
	state => {
		return {
			loading: state.loading,
			hasMore: state.has_more,
			stories: state.stories,
			distribution: state.distribution,
            active_view: state.active_view,
            section: state.section,
		}
	},
	dispatch => ({
		fetchStories (start = 0, view = 'date') {
			dispatch(fetchAllStories({start, view}));
		},
        loadStories () {
            dispatch(loadStories());
		},
        setUrlParams (params) {
            setUrlParams(params);
        }
	})
)(AllStories);