import React from 'react'
import NavigationWidget from 'navigation-widget/dist';
import 'navigation-widget/dist/index.css';
//import ProjectsData from "./data.json";
import {connect} from "react-redux";
import {
    fetchAll,

} from "./actions";

class App extends React.Component {

    componentDidMount() {
        let {projects, fetchAll} = this.props;
        if(!projects.length) {
            fetchAll();
        }
    }

    render(){

        let {projects} = this.props;

        if(!projects.length) return null;

        const widgetProps = {
            projects: projects,
            currentProject: window.navBarConfig.currentProject,
            containerClass: "nav-bar-inner-container",
        };

        return (
            <NavigationWidget {...widgetProps} />
        );
    }
}

const mapStateToProps = (state) => ({
    ...state
})

export default connect (
    mapStateToProps,
    {
        fetchAll,
    }
)(App);

