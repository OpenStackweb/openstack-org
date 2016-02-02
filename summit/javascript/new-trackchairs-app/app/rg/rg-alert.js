riot.tag('rg-alert', '<div each="{ opts.alerts }" class="alert { type }" onclick="{ onclick }"> <a class="close" aria-label="Close" onclick="{ parent.remove }" if="{ dismissable != false }"> <span aria-hidden="true">&times;</span> </a> <div class="body"> { msg } </div> </div>', 'rg-alert , [riot-tag="rg-alert"] { font-size: 0.9em; position: relative; top: 0; right: 0; left: 0; width: 100%; } rg-alert .alert, [riot-tag="rg-alert"] .alert{ position: relative; margin-bottom: 15px; } rg-alert .body, [riot-tag="rg-alert"] .body{ padding: 15px 35px 15px 15px; } rg-alert .close, [riot-tag="rg-alert"] .close{ position: absolute; top: 50%; right: 20px; line-height: 12px; margin-top: -6px; font-size: 18px; border: 0; background-color: transparent; color: rgba(0, 0, 0, 0.5); cursor: pointer; outline: none; } rg-alert .danger, [riot-tag="rg-alert"] .danger{ color: #8f1d2e; background-color: #ffced8; } rg-alert .information, [riot-tag="rg-alert"] .information{ color: #31708f; background-color: #d9edf7; } rg-alert .success, [riot-tag="rg-alert"] .success{ color: #2d8f40; background-color: #ccf7d4; } rg-alert .warning, [riot-tag="rg-alert"] .warning{ color: #c06329; background-color: #f7dfd0; }', function(opts) {var _this = this;

this.on('update', function () {
	opts.alerts.forEach(function (alert) {
		alert.id = Math.random().toString(36).substr(2, 8);
		if (!alert.timer && alert.timeout) {
			alert.startTimer = function () {
				alert.timer = window.setTimeout(function () {
					opts.alerts.splice(opts.alerts.indexOf(alert), 1);
					if (alert.onclose) alert.onclose();
					_this.update();
				}, alert.timeout);
			};
			alert.startTimer();
		}
	});
});

this.remove = function (e) {
	e.stopPropagation();
	if (e.item.onclose) e.item.onclose();
	window.clearTimeout(e.item.timer);
	opts.alerts.splice(opts.alerts.indexOf(e.item), 1);
};
});
