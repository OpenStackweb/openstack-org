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

export class ShareButtons extends React.Component {

    shareFacebook(e) {
        const {share_info} = this.props;
        FB.ui({method: 'share', href: share_info.event_url}, response => {})
    }

    shareMail(e) {
        $('#email-modal').modal('show');
        $('#email-form').validate();
    }

    sendEmail() {

        const {share_info} = this.props;

        var url = `api/v1/summits/current/schedule/${share_info.event_id}/share`;

        var request = {
            from: $('#email-from').val(),
            to: $('#email-to').val(),
            token: $('#email-token').val()
        }

        if (!$('#email-form').valid()) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            success: function () {
                $('#email-modal').modal('hide');
                swal('Email Sent', 'Email sent successfully', 'success');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var error = 'there was an issue with your request';
                if (xhr.status == 412) {
                    var response = $.parseJSON(xhr.responseText);
                    error = response.messages[0].message;
                }
                $('#email-modal').modal('hide');
                swal('Error', error, 'error');
            }
        });
    }

    shareTwitter(e) {
        const {share_info} = this.props;
        var text = share_info.social_summary != '' ? share_info.social_summary : share_info.tweet;
        const url = `https://twitter.com/intent/tweet?text=${text}&url=${share_info.event_url}`;
        const dim = 'left=50,top=50,width=600,height=260,toolbar=1,resizable=0'
        window.open(url, 'mywin', dim)
    }

    componentDidMount() {
        const {share_info} = this.props
        $('#email-modal').on('click', '[data-dismiss="modal"]', function (e) {
            e.stopPropagation();
        });
        this.loadFacebookSdk(share_info.fb_app_id);
    }

    loadFacebookSdk(appId) {
        window.fbAsyncInit = function () {
            FB.init({
                appId: appId,
                xfbml: true,
                status: true,
                version: 'v2.12'
            });
        };

        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    render() {
        const {share_info} = this.props;
        return (
            <div className="shared-buttons">
                <div className="facebook share_icon" onClick={(e) => this.shareFacebook(e)}>
                    <span className="fa-stack fa-lg">
                        <i className="fa fa-circle fa-stack-2x"></i>
                        <i className="fa fa-facebook fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <div className="twitter share_icon" onClick={(e) => this.shareTwitter(e)}>
                    <span className="fa-stack fa-lg">
                        <i className="fa fa-circle fa-stack-2x"></i>
                        <i className="fa fa-twitter fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <div className="email share_icon" onClick={(e) => this.shareMail(e)}>
                    <span className="fa-stack fa-lg">
                        <i className="fa fa-circle fa-stack-2x"></i>
                        <i className="fa fa-envelope fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
                <div id="email-modal" className="modal fade" role="dialog">
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                <h4 className="modal-title">Email</h4>
                            </div>
                            <div className="modal-body">
                                <form id="email-form">
                                    <div className="form-group">
                                        <label htmlFor="email-from">From:</label>
                                        <input type="email" className="form-control" id="email-from" required/>
                                    </div>
                                    <div className="form-group">
                                        <label htmlFor="email-to">To:</label>
                                        <input type="email" className="form-control" id="email-to" required/>
                                    </div>
                                    <input type="hidden" id="email-token" value={share_info.token}/>
                                </form>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-default" onClick={(e) => this.sendEmail(e)}>
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}