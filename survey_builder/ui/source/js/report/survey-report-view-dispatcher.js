function SurveyReportViewDispatcher() {

    riot.observable(this);

    this.EXPORT_TO_PDF  = 'EXPORT_TO_PDF';

    this.exportToPdf = function()
    {
        this.trigger(this.EXPORT_TO_PDF);
    };

}

var dispatcher = new SurveyReportViewDispatcher();

module.exports = dispatcher;