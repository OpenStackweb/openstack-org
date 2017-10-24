import React from 'react';
import { withScriptjs, withGoogleMap, GoogleMap, Marker, InfoWindow } from "react-google-maps";
import MarkerClusterer from "react-google-maps/lib/components/addons/MarkerClusterer";

const MAP = {
    defaultZoom: 3,
    defaultCenter: { lat: 45, lng: -30 },
    options: {
        minZoom: 3,
        maxZoom: 19
    }
};

const GoogleMapWrapper = withGoogleMap((props) => {
    return  <GoogleMap
                ref={props.onMapLoad}
                defaultZoom={ MAP.defaultZoom }
                defaultCenter={ MAP.defaultCenter }
                onBoundsChanged={props.onMapChange}
            >
                {props.showMarkers &&
                    <MarkerClusterer
                        averageCenter
                        enableRetinaIcons
                        gridSize={60}
                    >
                        {props.markers.map(marker => (
                            <Marker
                                key={marker.id}
                                position={{ lat: marker.lat, lng: marker.lng }}
                                onClick={() => props.onToggleInfoWindow(marker.id)}
                            >
                                {marker.isInfoWindowOpen &&
                                    <InfoWindow onCloseClick={() => props.onToggleInfoWindow(marker.id)}>
                                        <div>{marker.name}</div>
                                    </InfoWindow>
                                }
                            </Marker>
                        ))}
                    </MarkerClusterer>
                }
            </GoogleMap>
    }
);

export class GMap extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            markers: [],
        };

        this.handleMapLoad = this.handleMapLoad.bind(this);
        this.handleMapChange = this.handleMapChange.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        this.setState({ markers: nextProps.markers });
    }

    handleMapLoad(map) {
        this._mapComponent = map;
    }

    handleMapChange = () => {
        let visible_markers = this.props.markers.filter(m => {
            let marker_loc = new google.maps.LatLng(m.lat, m.lng);
            return (this._mapComponent.getBounds().contains(marker_loc)) ;
        });

        this.props.onChangeCallback(visible_markers);
    };

    handleMarkerClick = (marker_id) => {
        let markers = this.state.markers;
        let is_open = markers.find(m => m.id == marker_id).isInfoWindowOpen;
        markers.find(m => m.id == marker_id).isInfoWindowOpen = !is_open;

        this.setState({ markers: markers });
    };


    render() {
        return (
            <GoogleMapWrapper
                showMarkers={this.state.markers.length > 0}
                markers={this.state.markers}
                loadingElement={<div style={{ height: `100%` }} />}
                containerElement={<div style={{ height: `400px` }} />}
                mapElement={<div style={{ height: `100%` }} />}
                onMapLoad={this.handleMapLoad}
                onMapChange={this.handleMapChange}
                onToggleInfoWindow={this.handleMarkerClick}
            />
        );
    }
}

export default GMap;
