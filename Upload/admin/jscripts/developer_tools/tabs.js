var DevTools = (function($, dt) {
	dt.QuickTab = function(name, tabs) {
		var t, lName, bName;

		if (!tabs ||
			tabs.length == 0 ||
			$('#quick_tab_' + name).length == 0) {
			return;
		}

		this.name = name;
		this.tabs = [];
		this.$container = $('#quick_tab_' + name);

		for (t = 0; t < tabs.length; t++) {
			lName = 'qt_link_' + name + '_' + tabs[t];
			bName = 'qt_body_' + name + '_' + tabs[t];
			if (!$('#' + lName) ||
				!$('#' + bName)) {
				return;
			}
			this.tabs[tabs[t]] = {
				link: $('#' + lName),
				body: $('#' + bName),
			};
			if (!this.active) {
				this.active = tabs[t];
			}
		}
		this.getActive();
		this.hideAll();
		this.show();
		this.observeLinks()
	};

	function observeLinks() {
		var property;
		for (property in this.tabs) {
			if (this.tabs.hasOwnProperty(property)) {
				this.tabs[property].link.children('a').click($.proxy(this.doClick, this));
			}
		}
	}

	function doClick(event) {
		var target = event.target;
		event.preventDefault();
		if (!target || target.nodeName != 'A') {
			return;
		}

		this.show($(target).parent()[0].getAttribute('name'));
	}

	function getActive() {
		var pieces;

		if (window.location.href.indexOf('#') == -1) {
			return;
		}

		pieces = window.location.href.split('#');
		if (pieces.length <= 1) {
			return;
		}
		this.active = pieces[pieces.length - 1];
	}

	function show(tab) {
		if (this.active &&
			this.tabs &&
			this.tabs[this.active] &&
			this.tabs[this.active].body) {
			if (!tab) {
				this.showTab(this.active);
				return;
			}
			this.hideTab(this.active);
		}

		if (tab &&
			this.tabs &&
			this.tabs[tab] &&
			this.tabs[tab].body) {
			this.showTab(tab);
			this.active = tab;
		} else {
			this.showFirstAvailable();
		}
	}

	function showFirstAvailable() {
		var property;
		for (property in this.tabs) {
			if (this.tabs.hasOwnProperty(property)) {
				if (this.tabs[property].body) {
					this.show(property);
					return;
				}
			}
		}
	}

	function hideAll() {
		var property;
		for (property in this.tabs) {
			if (this.tabs.hasOwnProperty(property)) {
				this.hideTab(property);
			}
		}
	}

	function showTab(tab) {
		if (!tab ||
			!this.tabs[tab] ||
			!this.tabs[tab].link ||
			!this.tabs[tab].body) {
			return;
		}
		this.tabs[tab].link.children('a').hide();
		this.tabs[tab].link.children('span').show();
		this.tabs[tab].body.show();
	}

	function hideTab(tab) {
		if (!tab ||
			!this.tabs[tab] ||
			!this.tabs[tab].link ||
			!this.tabs[tab].body) {
			return;
		}
		this.tabs[tab].link.children('a').show();
		this.tabs[tab].link.children('span').hide();
		this.tabs[tab].body.hide();
	}

	dt.QuickTab.prototype = {
		show: show,
		showTab: showTab,
		hideTab: hideTab,
		getActive: getActive,
		hideAll: hideAll,
		doClick: doClick,
		observeLinks: observeLinks,
		showFirstAvailable: showFirstAvailable,
	};

	function newInstance(name, tabs) {
		dt.QuickTab.instances[name] = new dt.QuickTab(name, tabs);
	}

	function getInstance(name) {
		if (!name ||
			!dt.QuickTab.instances[name]) {
			return false;
		}
		return dt.QuickTab.instances[name];
	}

	dt.QuickTab.instances = {};
	dt.QuickTab.newInstance = newInstance;
	dt.QuickTab.getInstance = getInstance;

	return dt;
})(jQuery, DevTools || {});
