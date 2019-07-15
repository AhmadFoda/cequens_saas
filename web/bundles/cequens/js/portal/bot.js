var bot;
$(function () {

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {
            return;
        }
        js = d.createElement(s);
        js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    window.fbAsyncInit = function () {
        FB.init({
            appId: fappid,
            status: true,
            cookie: true,
            xfbml: true,
            version: 'v3.2'
        });

    };

    Handlebars.registerHelper('compare', function (lvalue, rvalue, options) {

        if (arguments.length < 3)
            throw new Error("Handlerbars Helper 'compare' needs 2 parameters");

        operator = options.hash.operator || "==";

        var operators = {
            '==': function (l, r) {
                return l == r;
            },
            '===': function (l, r) {
                return l === r;
            },
            '!=': function (l, r) {
                return l != r;
            },
            '<': function (l, r) {
                return l < r;
            },
            '>': function (l, r) {
                return l > r;
            },
            '<=': function (l, r) {
                return l <= r;
            },
            '>=': function (l, r) {
                return l >= r;
            },
            '&&': function (l, r) {
                return l && r;
            },
            '||': function (l, r) {
                return l || r;
            },
            'typeof': function (l, r) {
                return typeof l == r;
            }
        };

        if (!operators[operator])
            throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);

        var result = operators[operator](lvalue, rvalue);

        if (result) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    // isIn helper for checking if an element is present in an array.
    Handlebars.registerHelper('isIn', function (element, array, options) {
        if (!array || !element) return options.inverse(this);

        for (var i = 0; i < array.length; i++) {
            if (element == array[i])
                return options.fn(this);
        }
        return options.inverse(this);
    });

    function testAPI() {
        console.log('Welcome!  Fetching your information.... ');
        FB.api('/me', function (response) {
            console.log('Successful login for: ' + response.name);
            document.getElementById('status').innerHTML =
                'Thanks for logging in, ' + response.name + '!';
        });
    }

    function statusChangeCallback(response) {
        console.log('statusChangeCallback');
        console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            testAPI();
        } else {
            // The person is not logged into your app or we are unable to tell.
            /*document.getElementById('status').innerHTML = 'Please log ' +
                'into this app.';*/
        }
    };

    function updateFbstatus(e) {
        bot.updateFbstatus(e);
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function (response) {
            statusChangeCallback(response);
        });
    };

    bot = {

        urls: {
            create_application: applicationBaseUrl + '/portal/bot/doCreateBotApplication',
            update_application: applicationBaseUrl + '/portal/bot/doUpdateBotApplication',
        },

        constants: {},

        variables: {
            fb_user_is_logged_in: false,
            fb_user_pages: [],
        },

        init: function () {
            this.cacheDom();
            this.bindEvents();

        },

        cacheDom: function () {
            //Create New Application Form
            this.$createApplicationForm = $('#frm_bot_create_app');
            this.$botApplidationPlatformLst = this.$createApplicationForm.find('#bot_app_channel_lst');
            this.$botApplidationPlatformLstDetails = this.$createApplicationForm.find('#bot_app_channel_details');
            this.$saveApplicationActionBtn = this.$createApplicationForm.find('#btn_save_application');
        },

        //Bind Events
        bindEvents: function () {
            var self = this;
            self.$saveApplicationActionBtn.on('click', self.saveApplicationAction.bind(this));
            self.$botApplidationPlatformLst.on('change', self.fetchBotPlatformDetails.bind(this));
        },

        fetchBotPlatformDetails: function (e) {
            var self = this;
            self.$botApplidationPlatformLstDetails.html('');
            var tmpl_name = 'script#template-appPlatform' + self.$botApplidationPlatformLst.val();
            var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
            self.$botApplidationPlatformLstDetails.html(tmpl_module_setting());
            if (Number(self.$botApplidationPlatformLst.val()) == 1) {
                console.log('inside condition');
                $('.fb_advanced').css('display', 'none');
                FB.AppEvents.logPageView();
                FB.getLoginStatus(function (response) {
                    statusChangeCallback(response);
                });
                FB.XFBML.parse();
                $('#fb_app_is_advanced').on('change', self.showHideFbAdvancedFeatures.bind(this));
                self.$botApplidationPlatformLstDetails.find('.cs-select').select2();
            }
        },

        showHideFbAdvancedFeatures: function () {
            if ($('#fb_app_is_advanced').is(":checked")) {
                $('.fb_advanced').css('display', 'block');
                $('.fb_advanced input').prop('required', true);
            } else {
                $('.fb_advanced input').removeProp('required');
                $('.fb_advanced input').removeProp('aria-required');
                $('.fb_advanced').css('display', 'none');

            }
        },

        updateFbstatus: function () {
            var self = this;
            console.log('LOGIN RESPONSE::::');
            FB.getLoginStatus(function (response) {
                if (response.status === 'connected') {
                    console.log('User is connected with response ', response);
                    FB.XFBML.parse();
                    $('#fb_login_details').css('display', 'none');
                    self.fetchPageList(response);
                } else {
                    console.log('Login Failed');
                    //initiateFBLogin();
                }
            });
            //statusChangeCallback(response);
            //testAPI();
        },

        fetchPageList: function (response) {
            var self = this;
            var accessToken = response.authResponse.accessToken;
            self.variables.fb_user_pages = [];
            FB.api('/me/accounts', function (response) {
                $('#fb_login_details_pages').css('display', 'block');
                console.log('Successful accounts for: ', response);
                var pageList = response.data;
                self.variables.fb_user_pages = [];
                $.each(pageList, function (j) {
                    var page = pageList[j];
                    self.variables.fb_user_pages[page.id]=page;
                    $('#FACEBOOK_PAGE_ID').append($('<option/>', {
                        value: page.id,
                        text: page.name
                    }));
                });
                $('#FACEBOOK_PAGE_ID').select2();
                $('#FACEBOOK_PAGE_ID').trigger('change');

            });

        },

        saveApplicationAction: function (e) {
            e.preventDefault();
            var self = this;
            self.$saveApplicationActionBtn.attr("disabled", "disabled");
            if (self.validateApplicationAction(e)) {
                var channelType = Number(self.$botApplidationPlatformLst.val());
                var appName = self.$createApplicationForm.find('#input_application_name').val();
                var appDescrption = self.$createApplicationForm.find('#input_application_description').val();
                var applicationFormData = {
                    bot_name: appName,
                    bot_description: appDescrption,
                    bot_type: channelType,
                    bot_config: {},

                };
                if(channelType==1)
                {
                    var welcomeText = self.$createApplicationForm.find('#fb_welcome_text').val();
                    var language = self.$createApplicationForm.find('#fb_language').val();
                    var useMyApp = self.$createApplicationForm.find('#fb_app_is_advanced').is(":checked");
                    if(useMyApp)
                    {

                    }
                    var pageId = self.$createApplicationForm.find('#FACEBOOK_PAGE_ID').val();
                    applicationFormData.bot_config = {
                        FACEBOOK_WELCOME_TEXT: welcomeText,
                        FACEBOOK_LANGUAGE: language,
                        FACEBOOK_PAGE_NAME: self.variables.fb_user_pages[pageId].name,
                        FACEBOOK_PAGE_TOKEN: self.variables.fb_user_pages[pageId].access_token,
                        FACEBOOK_PAGE_ID: pageId,
                        USE_MY_APP: useMyApp,
                    };
                }
                else
                {
                    alert('Selected Channel not supported at this moment');
                    return ;
                }
                $.ajax({
                    type: 'post',
                    url: self.urls.create_application,
                    data: applicationFormData,
                    dataType: 'json',
                    cache: false,
                    timeout: 120000,
                    success: function (response) {
                        if (response.success) {
                            window.location = response.url;
                            self.$saveApplicationActionBtn.removeAttr("disabled");
                        } else {
                            alert('Error while updating application ' + response.msg);
                            self.$saveApplicationActionBtn.removeAttr("disabled");
                        }
                    },
                    error: function (response) {
                        alert('Error while updating application');
                        self.$saveApplicationActionBtn.removeAttr("disabled");
                    }
                });
            } else {

            }
        },

        //validate Application
        validateApplicationAction: function (e) {
            e.preventDefault();
            var self = this;
            if (self.$createApplicationForm.valid() && self.$createApplicationForm.validate()) {
                return true;
            } else {
                return false;
            }


        },

        //Update Application
        updateApplicationAction: function (e) {
            e.preventDefault();
            var self = this;
            if (!self.$updateApplicationForm.valid()) {
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
                        //window.location = response.url;
                        //self.$updateApplicationActionBtn.removeAttr("disabled");
                    } else {
                        alert('Error while updating application ' + response.msg);
                        //self.$updateApplicationActionBtn.removeAttr("disabled");
                    }
                },
                error: function (response) {
                    alert('Error while updating application');
                    //self.$updateApplicationActionBtn.removeAttr("disabled");
                }
            });
        },
    };

    bot.init();
});