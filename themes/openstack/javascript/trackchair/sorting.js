
jQuery(function ($) {

	$("#group-list").sortable({
	  items : "li:not(.unused-position)",
      update : function () {
		var order = $('#group-list').sortable('serialize');
  		$("#info").load(processingLink+order);
      }
    }).disableSelection();

	$("#member-list").sortable({
	  items : "li:not(.unused-position)",
      update : function () {
		var order = $('#member-list').sortable('serialize');
  		$("#info").load(processingLink+order);
      }
    }).disableSelection();    
    
});

