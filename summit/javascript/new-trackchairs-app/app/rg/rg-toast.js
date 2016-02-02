riot.tag('rg-toast', '<div class="toasts { opts.position }" if="{ opts.toasts.length > 0 }"> <div class="toast" each="{ opts.toasts }" onclick="{ parent.toastClicked }"> { text } </div> </div>', 'rg-toast .toasts, [riot-tag="rg-toast"] .toasts{ position: fixed; width: 250px; max-height: 100%; overflow-y: auto; background-color: transparent; z-index: 101; } rg-toast .toasts.topleft, [riot-tag="rg-toast"] .toasts.topleft{ top: 0; left: 0; } rg-toast .toasts.topright, [riot-tag="rg-toast"] .toasts.topright{ top: 0; right: 0; } rg-toast .toasts.bottomleft, [riot-tag="rg-toast"] .toasts.bottomleft{ bottom: 0; left: 0; } rg-toast .toasts.bottomright, [riot-tag="rg-toast"] .toasts.bottomright{ bottom: 0; right: 0; } rg-toast .toast, [riot-tag="rg-toast"] .toast{ padding: 20px; margin: 20px; background-color: rgba(0, 0, 0, 0.8); color: white; font-size: 13px; cursor: pointer; }', function(opts) {var _this = this;

if (!opts.position) opts.position = 'topright';

this.toastClicked = function (e) {
	if (e.item.onclick) e.item.onclick(e);
	if (e.item.onclose) e.item.onclose();
	window.clearTimeout(e.item.timer);
	opts.toasts.splice(opts.toasts.indexOf(e.item), 1);
};

this.on('update', function () {
	opts.toasts.forEach(function (toast) {
		toast.id = Math.random().toString(36).substr(2, 8);
		if (!toast.timer && !toast.sticky) {
			toast.startTimer = function () {
				toast.timer = window.setTimeout(function () {
					opts.toasts.splice(opts.toasts.indexOf(toast), 1);
					if (toast.onclose) toast.onclose();
					_this.update();
				}, toast.timeout || 6000);
			};
			toast.startTimer();
		}
	});
});
});
