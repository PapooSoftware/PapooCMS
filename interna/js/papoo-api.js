(function () {
	var actions = [{
		route: /^menu\/(?!0)\d+\/publish$/,
		callback: publishMenu
	}, {
		route: /^article\/(?!0)\d+\/publish$/,
		callback: publishArticle
	},{
		route: /^menu\/(?!0)\d+\/read-permission-all$/,
		callback: readPermissionAllMenu
	}, {
		route: /^article\/(?!0)\d+\/read-permission-all$/,
		callback: readPermissionAllArticle
	}, {
		route: /^order\/(?!0)\d+\/paid$/,
		callback: shopInvoicePaid
	}];

	var checkboxes = [];

	function startPapooAPICall(onSuccess, onError) {
		onSuccess = typeof onSuccess === "function" ? onSuccess : function () {};
		onError = typeof onError === "function" ? onError : function () {};

		var triggerElement = this;
		var route = this.getAttribute("data-papoo-api");

		var xhr = new XMLHttpRequest();
		// xhr.responseType = "json"; // wird vom IE nicht unterstuetzt
		xhr.onreadystatechange = function () {
			if (this.readyState == 4) {
				this.status == 200 ? onSuccess.call(triggerElement, JSON.parse(this.response), route) : onError.call(triggerElement, route);
			}
		}
		xhr.open("POST", "api.php?route="+encodeURIComponent(route), true);
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		xhr.send();
	}
	function checkboxSuccess(response, route) {
		// articles are assignable to multiple menu items => trigger all checkboxes
		checkboxes.forEach(function (checkbox) {
			if (checkbox.getAttribute("data-papoo-api") === route) {
				checkbox.checked = response.newState;
			}
			checkbox.addEventListener("change", routePapooAPICall);
		});
		this.checked = response.newState;
		this.disabled = false;
	}
	function checkboxError(route) {
		this.disabled = false;
	}

	function publishMenu() {
		this.disabled = true;
		startPapooAPICall.call(this, checkboxSuccess, checkboxError);
	}
	function publishArticle() {
		this.disabled = true;
		startPapooAPICall.call(this, checkboxSuccess, checkboxError);
	}
	function readPermissionAllMenu() {
		this.disabled = true;
		startPapooAPICall.call(this, checkboxSuccess, checkboxError);
	}
	function readPermissionAllArticle() {
		this.disabled = true;
		startPapooAPICall.call(this, checkboxSuccess, checkboxError);
	}
	function shopInvoicePaid() {
		this.disabled = true;
		startPapooAPICall.call(this, checkboxSuccess, checkboxError);
	}

	function routePapooAPICall() {
		var route = this.getAttribute("data-papoo-api");
		actions.reduce(function (callback, action) {
			return action.route.test(route) ? action.callback : callback;
		}, function () {}).call(this);
	}

	window.addEventListener("DOMContentLoaded", function () {
		checkboxes = Array.prototype.slice.call(document.querySelectorAll("input[type='checkbox'][data-papoo-api]"));
		checkboxes.forEach(function (checkbox) {
			checkbox.addEventListener("change", routePapooAPICall);
		});
	});
})();