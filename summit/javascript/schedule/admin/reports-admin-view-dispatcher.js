function ReportsAdminViewDispatcher() {

    riot.observable(this);

    this.SAVE_PRESENTATION_REPORT              = 'SAVE_PRESENTATION_REPORT';
    this.GET_PRESENTATION_REPORT               = 'GET_PRESENTATION_REPORT';
    this.SAVE_SPEAKER_REPORT                   = 'SAVE_SPEAKER_REPORT';
    this.SAVE_ROOM_REPORT                      = 'SAVE_ROOM_REPORT';
    this.EXPORT_PRESENTATION_REPORT            = 'EXPORT_PRESENTATION_REPORT';
    this.EXPORT_SPEAKER_REPORT                 = 'EXPORT_SPEAKER_REPORT';
    this.EXPORT_ROOM_REPORT                    = 'EXPORT_ROOM_REPORT';

    this.saveReport = function(report)
    {
        switch (report) {
            case 'presentation_report' :
                this.trigger(this.SAVE_PRESENTATION_REPORT, report);
                break;
            case 'speaker_report' :
                this.trigger(this.SAVE_SPEAKER_REPORT, report);
                break;
            case 'room_report' :
                this.trigger(this.SAVE_ROOM_REPORT, report);
                break;
        }

    }

    this.getReport = function(report)
    {
        switch (report) {
            case 'presentation_report' :
                this.trigger(this.GET_PRESENTATION_REPORT);
                break;
        }
    }

    this.exportReport = function(report)
    {
        switch (report) {
            case 'presentation_report' :
                this.trigger(this.EXPORT_PRESENTATION_REPORT);
                break;
            case 'speaker_report' :
                this.trigger(this.EXPORT_SPEAKER_REPORT);
                break;
            case 'room_report' :
                this.trigger(this.EXPORT_ROOM_REPORT);
                break;
        }
    }


}

var dispatcher = new ReportsAdminViewDispatcher();

module.exports = dispatcher;