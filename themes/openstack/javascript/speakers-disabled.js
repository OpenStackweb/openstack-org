jQuery(document).ready(function($){
	
	// outline empty fields in red
	$("#CallForSpeakersForm_CallForSpeakersForm .text[value='']").addClass("empty-field");
	// not needed on other field
	$("#CallForSpeakersForm_CallForSpeakersForm_Other").removeClass("empty-field");			
})
