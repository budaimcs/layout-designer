var JQuery = $;

(function($) {

$.newApp = {}

$.newApp.app = null;
    
$.newApp.bootstrap = function() {
	$.newApp.app = new $.newApp.Router(); 
	Backbone.history.start({pushState: true});
};



}) (JQuery);