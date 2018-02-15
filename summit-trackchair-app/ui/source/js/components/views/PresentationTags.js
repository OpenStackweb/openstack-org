import React from 'react';
import {browserHistory} from 'react-router';
import URL from '../../utils/url';

export default class PresentationTags extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {};
	}

	handleClick(e, tag) {
		e.preventDefault();

        browserHistory.push(
            URL.create('/browse', {search: 'Tag:'+tag})
        );
	}

	render() {
		let {tags} = this.props;

		return (
			<div className="presentation-tags">
		        <div className="row">
					<div className="col-md-12">
					{tags.map(t =>
						{
							if (t.Tag != '') {
								return (
									<a className="btn btn-xs btn-default" href="" onClick={(e) => this.handleClick(e, t.Tag)}>
                                        {t.Tag}
									</a>
								);
							}
						}
					)}
					</div>
				</div>
			</div>
		);		
	}	
}