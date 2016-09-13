module.exports = {
  entry: {
    "javascript/summit-highlights":"./javascript/summit-highlights.js",
    "javascript/schedule/schedule":"./javascript/schedule/schedule.js",
    "javascript/schedule/event-detail":"./javascript/schedule/event-detail.js",
    "javascript/schedule/my-schedule-view":"./javascript/schedule/my-schedule-view.js",
    "javascript/schedule/full-schedule-view":"./javascript/schedule/full-schedule-view.js",
    "javascript/schedule/share-buttons":"./javascript/schedule/share-buttons.js",
    "javascript/forms/tagmanagerfield/tagmanagerfield":"./javascript/forms/tagmanagerfield/tagmanagerfield.js",
    "javascript/schedule/event-list":"./javascript/schedule/event-list.js",
    "javascript/schedule/admin/schedule-admin-view":"./javascript/schedule/admin/schedule-admin-view.js",
    "javascript/schedule/admin/attendees-admin-view":"./javascript/schedule/admin/attendees-admin-view.js",
    "javascript/schedule/admin/reports-admin-view":"./javascript/schedule/admin/reports-admin-view.js",
    "javascript/schedule/admin/speakers-admin-view":"./javascript/schedule/admin/speakers-admin-view.js",
    "javascript/schedule/admin/promocode-admin-view":"./javascript/schedule/admin/promocode-admin-view.js",
    "javascript/schedule/admin/events-bulk-view":"./javascript/schedule/admin/events-bulk-view.js", 
	"javascript/schedule/admin/room-metrics-view":"./javascript/schedule/admin/room-metrics-view.js" 
  },
  
  output: {
    path: __dirname ,
    filename: "[name].bundle.js"
  }
};