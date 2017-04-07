function FeedbackFormDispatcher() {

    riot.observable(this);

    this.SUBMIT_FEEDBACK = 'SUBMIT_FEEDBACK';

    this.submitFeedback = function(feedback)
    {
        this.trigger(this.SUBMIT_FEEDBACK, feedback);
    }
}

var dispatcher = new FeedbackFormDispatcher();

module.exports = dispatcher;