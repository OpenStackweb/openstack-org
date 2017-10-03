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
import React from 'react'
import EventCommentList from './event-comment-list';
import EventCommentForm from './event-comment-form';
import { connect } from 'react-redux';
import {
    postNewComment
} from '../../actions';

class EventComments extends React.Component
{
    constructor(props){
        super(props);
        this.onHandleSubmit = this.onHandleSubmit.bind(this);
    }

    onHandleSubmit(newComment){
        console.log(newComment);
        this.props.postNewComment(newComment);
    }

    /**
     * @returns {boolean|*}
     */
    shouldShowCommentForm(){
        const { currentUser } = this.props;
        return currentUser != null &&
        this.shouldShowComments() &&
        !currentUser.has_feedback;
    }

    shouldShowComments(){
        const { event } = this.props;
        return event.has_ended &&
        event.allow_feedback;
    }

    render(){
        if(this.shouldShowComments())
            return(
                <div className="event-comments">
                    {this.shouldShowCommentForm() &&
                    <EventCommentForm {...this.props} onHandleSubmit={this.onHandleSubmit}/>
                    }
                    <EventCommentList {...this.props}/>
                </div>);
        return <div className="event-comments"></div>;
    }
}

function mapStateToProps(state) {
    return {
        event: state.event,
        currentUser: state.currentUser
    }
}

export default connect(mapStateToProps, {
    postNewComment,
})(EventComments)