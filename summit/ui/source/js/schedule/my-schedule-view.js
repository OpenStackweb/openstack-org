import React from 'react';
import ReactDOM from 'react-dom';
import MyScheduleView from '../components/schedule/my-schedule-view'

ReactDOM.render(
    <MyScheduleView
        base_url={base_url}
        events={events}
        isLoggedUser={is_logged_user}
        should_show_venues={should_show_venues}
        summitId={summit_id}
        backUrl={ backUrl }
        pdfUrl={pdfUrl}
        goBack={goBack}
        timeZone={timeZone}
    />,
    document.getElementById('my-schedule-view-container')
);