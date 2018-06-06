const EventListFilter = {

    events(filters, events, summit) {
        const { tags, room, going, tracks, levels, favorites,
                event_types, track_groups } = filters

        const unmatchedEvents = events.filter(event => {
            return (
                track_groups && ! this.matchTrackGroups(event, track_groups, summit) ||
                event_types  && ! this.matchEventTypes(event, event_types) ||
                tracks       && ! this.matchTracks(event, tracks) ||
                levels       && ! this.matchLevels(event, levels) ||
                tags         && ! this.matchTags(event, tags) ||
                room         && ! this.matchRoom(event, room) ||
                going        && ! this.matchGoing(event, going) ||
                favorites    && ! this.matchFavorites(event, favorites)
            )
        });

        return unmatchedEvents.map(event => event.id)
    },

    matchTrackGroups(event, filterValue, summit) {
        if ( ! event.hasOwnProperty('track_id')) {
            return false
        }
        return filterValue.some(groupId => {
            return summit.category_groups[parseInt(groupId)].tracks.indexOf(
                parseInt(event.track_id)
            ) >= 0;
        })
    },

    matchEventTypes(event, filterValue) {
        return filterValue.indexOf(event.type_id.toString()) >= 0
    },

    matchTracks(event, filterValue) {
        if ( ! event.hasOwnProperty('track_id')) {
            return false;
        }
        return filterValue.indexOf(event.track_id.toString()) >= 0
    },

    matchLevels(event, filterValue) {
        if ( ! event.hasOwnProperty('level')) {
            return false;
        }
        return filterValue.indexOf(event.level.toLowerCase()) >= 0
    },

    matchTags(event, filterValue) {
        return event.tags_id.some(tagId => {
            return filterValue.indexOf(tagId.toString()) >= 0
        });
    },

    matchRoom(event, filterValue) {
        if ( ! event.hasOwnProperty('location_id')) {
            return false;
        }
        return filterValue.indexOf(event.location_id) >= 0
    },

    matchGoing(event, filterValue) {
        return event.going === true
    },

    matchFavorites(event, filterValue) {
        return event.favorite === true
    },
}

export default EventListFilter
