import React from 'react';

export default function (pollFunc, pollInterval) {
	return function (WrappedComponent) {
		class PollingComponent extends React.Component {

			constructor (props) {
				super(props);
				this._interval = null;
			}

			componentDidMount () {
				if(pollFunc && pollInterval) {
					this._clearInterval();
					this._interval = window.setInterval(() => {
						pollFunc.call(this);
					}, pollInterval);
				}
			}

			componentWillUnmount () {
				this._clearInterval();
			}

			_clearInterval () {
				if(this._interval) {
					window.clearInterval(this._interval);
				}				
			}

			render () {
				return React.createElement(
					WrappedComponent,
					this.props,
					this.props.children
				);
			}
		}

		PollingComponent.displayName = (WrappedComponent.displayName || WrappedComponent.name || 'Component');

		return PollingComponent;
	}
}