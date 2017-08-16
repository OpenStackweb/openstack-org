import React, { Component } from 'react'

class EventActions extends Component {

    constructor(props) {
        super(props)
        this.toggle = this.toggle.bind(this)
        this.dispatch = this.dispatch.bind(this)
    }

    render() {
        const { event } = this.props
        return (
        <div onClick={this.toggle} id={`event_actions_${event.id}`}
        className="event-actions-container col-sm-1 col-xs-1">
            <a ref="popup" role="button" title="event actions" data-toggle="dropdown"
            id={`event_action_menu_${event.id}`} className="event-actions-menu"
            aria-haspopup="true" aria-expanded="false">
                <span className="caret caret-event-actions"></span>
            </a>
            <ul className="dropdown-menu dropdown-menu-event-actions"
            aria-labelledby={`event_action_menu_${event.id}`}>
                {this.renderActions()}
            </ul>
        </div>
        )
    }

    renderActions() {
        const { event,
            ScheduleProps: { summit: { current_user } }
        } = this.props

        return ActionList.map((action, index) => {

            if (action.separator) {
                return <li key={index} role="separator" className="divider"></li>
            }

            const enabled = action.enabled(event, current_user)
            const classes = `${action.type}-action ${enabled ? '' : 'disabled'}`

            return action.visible(event, current_user) && (
            <li key={index} className={`event-action ${classes}`}>
                <a data-type={action.type} className="event-action-link"
                onClick={e => enabled && this.dispatch(e, action)}>
                    {action.icon && <i className={`fa ${action.icon}`} />}
                    &nbsp;{action.title}
                </a>
            </li>
            )
        })
    }

    toggle(e) {
        e.preventDefault()
        e.stopPropagation()
        $(this.refs.popup).dropdown('toggle')
    }

    dispatch(e, action) {
        e.preventDefault()
        e.stopPropagation()

        const { type } = action
        const { ScheduleProps: { summit: { current_user } } } = this.props

        if (['going', 'watch', 'rsvp'].indexOf(type) >= 0 && ! current_user) {
            return this.showLoginAlert()
        }
        /*if (['going', 'rsvp'].indexOf(type) >= 0 && ! current_user.is_attendee) {
            return this.showEventBriteAlert()
        }*/

        return action.click(this.props)
    }

    showLoginAlert() {
        swal({
            title: 'Login Required',
            text: "You need to log in to proceed with this action.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Log in'
        }).then(function () {
            const url = encodeURIComponent(window.location.href)
            window.location = "/Security/login?BackURL=" + url
        })
    }

    showEventBriteAlert() {
        swal({
            title: 'EventBrite Ticket Required',
            text: "Only attendees can use this function. Enter your Eventbrite order number in My Summit if you are an attendee.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Add ticket'
        }).then(function () {
            const url = encodeURIComponent(window.location.href)
            window.location = "/profile/attendeeInfoRegistration?BackURL=" + url
        })
    }
}

EventActions.propTypes = {
    event: React.PropTypes.object.isRequired,
    ScheduleProps: React.PropTypes.object.isRequired,
    addEventToRsvp: React.PropTypes.func.isRequired,
    addEventToSchedule: React.PropTypes.func.isRequired,
    addEventToFavorites: React.PropTypes.func.isRequired,
    removeEventFromRsvp: React.PropTypes.func.isRequired,
    removeEventFromSchedule: React.PropTypes.func.isRequired,
    removeEventFromFavorites: React.PropTypes.func.isRequired,
}

const shareFacebook = ({ event }) => {
    FB.ui({ method: 'share', href: event.url}, response => {})
}

const shareTwitter = ({ event, ScheduleProps: { summit } }) => {
    const text = event.social_summary
        ? event.social_summary
        : summit.share_info.tweet

    const url = `https://twitter.com/intent/tweet?text=${text}&url=${event.url}`
    const dim = 'left=50,top=50,width=600,height=260,toolbar=1,resizable=0'

    window.open(url, 'mywin', dim)
}

const ActionList = [
    {
        type: 'rsvp',
        icon: 'fa-check-circle',
        title: 'RSVP',
        click: ({ addEventToRsvp, event }) => addEventToRsvp(event),
        enabled: (event, user) => ! (
            ! event.going
            && event.has_rsvp
            && event.rsvp_seat_type === 'FULL'
        ),
        visible: (event, user) => (
            ! event.going
            && event.has_rsvp
        ),
    },
    {
        type: 'unrsvp',
        icon: 'fa-times-circle',
        title: 'unRSVP',
        click: ({ removeEventFromRsvp, event }) => removeEventFromRsvp(event),
        enabled: () => true,
        visible: (event, user) => (
            event.going
            && event.has_rsvp
            && ! event.rsvp_external
            && user && user.is_attendee
        ),
    },
    {
        type: 'going',
        icon: 'fa-check-circle',
        title: 'Schedule',
        click: ({ addEventToSchedule, event }) => addEventToSchedule(event),
        enabled: () => true,
        visible: (event, user) => (
            ! event.has_rsvp
            && ! event.going
        ),
    },
    {
        type: 'not-going',
        icon: 'fa-times-circle',
        title: 'UnSchedule',
        click: ({ removeEventFromSchedule, event }) => removeEventFromSchedule(event),
        enabled: () => true,
        visible: (event, user) => (
            (
                ! event.has_rsvp
                && event.going
                || (
                    event.has_rsvp
                    && event.rsvp_external
                    && event.going
                )
            )
            && user && user.is_attendee
        ),
    },
    {
        type: 'watch',
        icon: 'fa-bookmark',
        title: 'Watch Later',
        click: ({ addEventToFavorites, event }) => addEventToFavorites(event),
        enabled: () => true,
        visible: (event, user) => (
            ! event.favorite
        ),
    },
    {
        type: 'unwatch',
        icon: 'fa-bookmark-o',
        title: 'Do not Watch Later',
        click: ({ removeEventFromFavorites, event }) => removeEventFromFavorites(event),
        enabled: () => true,
        visible: (event, user) => (
            event.favorite
        ),
    },
    {
        type: 'share-facebook',
        icon: 'fa-facebook-official',
        title: 'Share on Facebook',
        click: shareFacebook,
        enabled: () => true,
        visible: () => true,
    },
    {
        type: 'share-twitter',
        icon: 'fa-twitter-square',
        title: 'Share on Twitter',
        click: shareTwitter,
        enabled: () => true,
        visible: () => true,
    },
    {
        separator: true,
    },
    {
        type: 'cancel',
        icon: false,
        title: 'Cancel',
        click: () => false,
        enabled: () => true,
        visible: () => true,
    },
]

export default EventActions
