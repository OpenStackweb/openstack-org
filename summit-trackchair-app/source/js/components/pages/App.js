import React from 'react';
import NavigationBar from '../views/NavigationBar';
import ErrorMessage from '../containers/ErrorMessage';
import {connect} from 'react-redux';
import {fetchSummit} from '../../actions';
import Bounce from '../ui/loaders/Bounce';

class App extends React.Component {

    componentDidMount() {
        this.props.fetch();
    }
    
    render () {
    	if(!this.props.isReady) {
    		return <Bounce />
    	}
        return (
            <div>
            	<ErrorMessage />
			    <div id="wrapper">
			        <div id="page-wrapper" className="gray-bg">
			        	<div className="row border-bottom white-bg">			        	
                			<NavigationBar />
                		</div>
			            <div className="row">
			            	{this.props.children}
			            </div>            
                	</div>
               	</div>
            </div>
        );
    }
}

export default connect(
    state => ({
    	errorMsg: state.main.errorMsg,
    	isReady: (state.summit.data !== null)
    }),
    dispatch => ({
        fetch () {
            dispatch(fetchSummit(window.TrackChairAppConfig.summitID))
        }
    })
)(App);