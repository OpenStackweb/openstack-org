function EventsBulkViewDispatcher() {

    riot.observable(this);

    this.SAVE_PRESENTATIONS              = 'SAVE_PRESENTATIOS';
    this.GET_PRESENTATIONS               = 'GET_PRESENTATIONS';

    this.saveList = function(list)
    {
        switch (list) {
            case 'presentations' :
                this.trigger(this.SAVE_PRESENTATIONS, list);
                break;
        }

    }

    this.getList = function(list)
    {
        switch (list) {
            case 'presentations' :
                this.trigger(this.GET_PRESENTATIONS);
                break;
        }
    }
}

var dispatcher = new EventsBulkViewDispatcher();

module.exports = dispatcher;