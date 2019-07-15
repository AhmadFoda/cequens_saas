$(function () {
	var verify = {
		urls: {
			create_application: applicationBaseUrl + '/portal/verify/doCreateApplication',
			update_application: applicationBaseUrl + '/portal/verify/doUpdateApplication',
		},
		constants: {},
		variables: {},
		init: function () {
			this.cacheDom();
			this.bindEvents();
		},
		cacheDom: function () {
			//Create New Application Form
			this.$createApplicationForm = $('#frm_verify_create_app');
			this.$saveApplicationActionBtn = this.$createApplicationForm.find('#btn_save_application');

			//Create New Application Form
			this.$updateApplicationForm = $('#frm_verify_update_app');
			this.$updateApplicationActionBtn = this.$updateApplicationForm.find('#btn_update_application');
			this.$updateApplicationForm.find('.cs-select').select2();
			this.$updateApplicationForm.find('.cs-select').trigger('change');
		},

		//Bind Events
		bindEvents: function () {
			var self = this;
			self.$saveApplicationActionBtn.on('click', self.saveApplicationAction.bind(this));
			self.$updateApplicationActionBtn.on('click', self.updateApplicationAction.bind(this));
		},

		//Save Application
		saveApplicationAction: function (e) {
			e.preventDefault();
			var self = this;
			if(!self.$createApplicationForm.valid())
			{
				return true;
			}
			self.$saveApplicationActionBtn.attr("disabled", "disabled");
			var form = self.$createApplicationForm[0];
			var applicationFormData = new FormData(form);
			$.ajax({
				type: 'post',
				url: self.urls.create_application,
				data: applicationFormData,
				dataType: 'json',
				contentType: false,
				processData: false,
				cache: false,
				timeout: 120000,
				success: function (response) {
					if (response.success) {
						window.location = response.url;
						self.$saveApplicationActionBtn.removeAttr("disabled");
					}
					else {
						alert('Error while creating application '+response.msg);
						self.$saveApplicationActionBtn.removeAttr("disabled");
					}
				},
				error: function (response) {
					alert('Error while creating application');
					self.$saveApplicationActionBtn.removeAttr("disabled");
				}
			});
		},

		//Update Application
		updateApplicationAction: function (e) {
			e.preventDefault();
			var self = this;
			if(!self.$updateApplicationForm.valid())
			{
				return true;
			}
			self.$updateApplicationActionBtn.attr("disabled", "disabled");
			var form = self.$updateApplicationForm[0];
			var applicationFormData = new FormData(form);
			$.ajax({
				type: 'post',
				url: self.urls.update_application,
				data: applicationFormData,
				dataType: 'json',
				contentType: false,
				processData: false,
				cache: false,
				timeout: 120000,
				success: function (response) {
					if (response.success) {
						window.location = response.url;
						self.$updateApplicationActionBtn.removeAttr("disabled");
					}
					else {
						alert('Error while updating application '+response.msg);
						self.$updateApplicationActionBtn.removeAttr("disabled");
					}
				},
				error: function (response) {
					alert('Error while updating application');
					self.$updateApplicationActionBtn.removeAttr("disabled");
				}
			});
		},
	};
	verify.init();
});