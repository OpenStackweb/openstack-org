import React from 'react';
import ReactDOM from 'react-dom';
import Schedule from 'summit-schedule-app/lib/schedule';


ReactDOM.render(
  <Schedule
    summitId={summitId}
    user={current_user}
    apiAccessToken={accessToken}
    apiUrl={apiUrl}
    scheduleHost="https://github.com"
    scheduleBase={scheduleUrl}
  />,
  document.getElementById('os-schedule-react')
);