$(function () {
    'use strict';

    Handlebars.registerHelper('compared', function (lvalue, operator, rvalue, options) {

        var operators, result;

        if (arguments.length < 3) {
            throw new Error("Handlerbars Helper 'compared' needs 2 parameters");
        }

        if (options === undefined) {
            options = rvalue;
            rvalue = operator;
            operator = "===";
        }

        operators = {
            '==': function (l, r) {
                return l == r;
            },
            '===': function (l, r) {
                return l === r;
            },
            '!=': function (l, r) {
                return l != r;
            },
            '!==': function (l, r) {
                return l !== r;
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
            'typeof': function (l, r) {
                return typeof l == r;
            }
        };

        if (!operators[operator]) {
            throw new Error("Handlerbars Helper 'compare' doesn't know the operator " + operator);
        }

        result = operators[operator](lvalue, rvalue);

        if (result) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }

    });

    Handlebars.registerHelper('compare', function (lvalue, rvalue, options) {

        if (arguments.length < 3)
            throw new Error("Handlerbars Helper 'compare' needs 2 parameters");

        var operator = options.hash.operator || "==";

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

    var tokenProvider = new Chatkit.TokenProvider({
        url: `https://us1.pusherplatform.io/services/chatkit_token_provider/v1/bb957f41-bf24-4f23-a015-1f51ceafb1b2/token`
    })

    var chatManager = new Chatkit.ChatManager({
        instanceLocator: "v1:us1:bb957f41-bf24-4f23-a015-1f51ceafb1b2",
        userId: "k.mohamed@cequens.com",
        tokenProvider
    });

    var currentUserObj;


    var inbox = {
        urls: {
            pusher_token_provider: 'https://us1.pusherplatform.io/services/chatkit_token_provider/v1/bb957f41-bf24-4f23-a015-1f51ceafb1b2/token',
            list_all_messages: applicationBaseUrl + "/portal/inbox/listAll",
        },

        constants: {
            tokenProvider: null,
            chatManager: null,
            typing_throttle_time: 200,
            emojis : [
                {"tag":"verify_identity","desc":"Ask for identity verification process","text":"First we need to verify your identity using our automated verification process"},
                {"tag":"thank_you_verify_identity","desc":"Thank you after identity verification","text":"Thank you for completing verification process succesfully"},
            ]
        },

        variables: {
            state: {
                unassigned: undefined,
                assigned: undefined,
                user: undefined,
                current_room: {},
                current_room_obj: {},
                current_sender: {},
                rooms: new Map(),
                currentUser: [],
                current_timer_id: {},
                current_message_index: 0,
                current_room_users: {},
                current_user_typing_publish: true,
                current_selected_bot: 'all',
            },

        },

        initPusher: function (currentUserObj) {
            var self = this;

            //initialize rooms variable
            self.variables.state.rooms = new Map();
            $.each(currentUserObj.rooms, function (j) {
                var roomObj = currentUserObj.rooms[j];
                console.log('adding new rooms to array ', roomObj);
                self.variables.state.rooms.set('' + roomObj.id, currentUserObj.rooms[j]);
            });

            //Initialize chatkit currentUser Object
            self.variables.state.currentUser = currentUserObj;
        },

        init: function (currentUser, joinablerooms) {
            var self = this;
            this.cacheDom();
            this.bindEvents();
            this.initPusher(self.variables.state.user);
            this.$emailList.length && this.loadMessages();
        },

        cacheDom: function () {
            this.$listViewItem = $('div.list-view-wrapper.item');
            this.$emailList = $('[data-email="list"]');
            this.$emailOpened = $('[data-email="opened"]');
            this.$listRefreshLink = $('.list-refresh');
        },

        bindEvents: function () {
            var self = this;

            //Listen to refresh rooms
            self.$listRefreshLink.on('click', self.loadMessages.bind(this));

            //Listen to select room
            self.bindListEvent();

            //Listen to agent send message
            $('#sendMessageFromAgent').submit(function (e) {
                e.preventDefault();
                e.stopPropagation();
                    var message = $('textarea#exampleFormControlTextarea2').val();
                    console.log('[pages.email.js message val] '+JSON.stringify(message));
                $('textarea#exampleFormControlTextarea2').attr('disabled', 'disabled');
                $('#btn_sendMessage').attr('disabled', 'disabled');

                console.log("Application base URL :: ".applicationBaseUrl);
                $.ajax({
                    type: 'post',
                    url: "https://workflow.cequens.net" + "/portal/inbox/sendMessage",
                    data: {
                        roome_id: Number(self.variables.state.current_room),
                        message: message,
                        room_name: self.variables.state.current_room_obj.name.substr(3)
                    },
                    dataType: 'json',
                    cache: false,
                    timeout: 120000,
                    success: function (response) {
                        if (response.success) {

                            $('textarea#exampleFormControlTextarea2').removeAttr('disabled');
                            $('#btn_sendMessage').removeAttr('disabled');
                            $('textarea#exampleFormControlTextarea2').val('');
                        } else {
                            console.log('Error while sending msg ');
                            $('textarea#exampleFormControlTextarea2').removeAttr('disabled');
                            $('#btn_sendMessage').removeAttr('disabled');
                            $('textarea#exampleFormControlTextarea2').val('');
                        }
                    },
                    error: function (response) {
                        console.log('Error while sending msg');
                        $('textarea#exampleFormControlTextarea2').removeAttr('disabled');
                        $('#btn_sendMessage').removeAttr('disabled');
                        $('textarea#exampleFormControlTextarea2').val('');
                    }
                });


                //$.post(
                //	applicationBaseUrl+"/portal/inbox/sendMessage",
                //	{roome_id: self.variables.state.current_room,message:message},
                //	function(result){
                //});
            });

            //Listen To Update Conversation status
            $('select#conversationStatus').on('change', function (e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Alert Callback");
                self.loadMessages();
                clearInterval(self.variables.state.current_timer_id);
                $('select#conversationStatus').attr('disabled', 'disabled');
                $('#sendMessage').css("display", "none");
                self.$emailOpened.find('.chat-view').find('.chat-inner').html('');
                $('#chat').hide();
                $('.no-result').show();
                $('.actions-dropdown').toggle();
                $.post(
                    applicationBaseUrl + "/portal/inbox/updateConversaionStatus",
                    {room_id: self.variables.state.current_room, status: this.value},
                    function (result) {
                        //chatManager.connect().then(
                        //	currentUser => {
                        //		currentUserObj = currentUser;
                        //		inbox.initPusher(currentUserObj);
                        //	}
                        //).catch(error => {
                        //	console.error("error:", error);
                        //});
                        $('select#conversationStatus').val('open');
                        $('select#conversationStatus').removeAttr('disabled');
                    });
            });
        },

        bindListEvent: function () {
            var self = this;
            $('body').on('click', '.item .checkbox', function (e) {
                e.stopPropagation();
            });
            $('body.item').unbind('click');
            $('body').on('click', '.item', function (e) {
                e.preventDefault();
                e.stopPropagation();
                clearInterval(self.variables.state.current_timer_id);
                var objEvent = e;
                var obj = this;
                self.variables.state.current_message_index = 0;
                self.variables.state.current_room = $(this).attr('data-email-id');
                self.variables.state.current_room_obj = self.variables.state.rooms.get($(this).attr('data-email-index'));
                console.log('current_room_obj', self.variables.state.current_room_obj);
                console.log('current_room obj', self.variables.state.current_room);

                //Subscribe to room and fetch last 100 messages
                self.variables.state.currentUser.subscribeToRoom({
                    roomId: self.variables.state.current_room,
                    hooks: {
                        onMessage: message => {
                            self.addNewMessage(message);
                            console.log('Received new message: ', message);
                        }
                    },
                    messageLimit: 100
                }).catch(err => {
                    console.log(`Error subscribing to  room : ${err}`)
                });
                self.variables.state.current_user_typing_publish = true;

                // initialize autoreply
                var autoreplyList = $.map(self.constants.emojis, function(value, i) {
                    return {'id':i,'tag':value.tag, 'text':value.text,'desc':value.desc};
                });
                console.log('QuickReplies => ',autoreplyList);

                //Listen to Quick Replies
                $('#exampleFormControlTextarea2').atwho({
                    at: '/',
                    displayTpl: "<li>${tag}::${desc}</li>",
                    insertTpl: "${text}",
                    data: autoreplyList,
                    searchKey: "tag",
                    headerTpl: "<div class='card-header clearfix'><h5>Select a tag</h5></div>",
                });
                $('.atwho-view').addClass('card social-card share col1');

                //Uncomment To Enable Triggering Is Typing Event
                /*
                $('#exampleFormControlTextarea2').unbind('keyup');
				$('#exampleFormControlTextarea2').on('keyup', function(event) {
					if(self.variables.state.current_user_typing_publish) {
						$.ajax({
							type: 'post',
							url: applicationBaseUrl+"/portal/inbox/triggerIsTyping",
							data: {
								room_id: Number(self.variables.state.current_room),
								room_name: self.variables.state.current_room_obj.name.substr(3)
							},
							dataType: 'json',
							cache: false,
							timeout: 120000,
							success: function (response) {
								if (response.success) {

								}
								else {

								}
							},
							error: function (response) {

							}
						});
						self.variables.state.current_user_typing_publish = false;
						setTimeout(function() {
							self.variables.state.current_user_typing_publish = true;
						}, self.constants.typing_throttle_time);

					}
				});
*/
                console.log('CurrentUserUsers => ', currentUserObj.users);
                console.log('currentSelectedRoomObj => ', self.variables.state.current_room_obj);
                self.$emailOpened.find('.chat-view').find('.chat-inner').html('');
                var thumbnailWrapper = $(obj).find('.thumbnail-wrapper');
                var thumbnailClasses = thumbnailWrapper.attr('class').replace('d32', 'd48');
                self.$emailOpened.find('.thumbnail-wrapper').html(thumbnailWrapper.html()).attr('class', thumbnailClasses);
                $('.item').removeClass('active');
                $(obj).addClass('active');
            });

            // Toggle messages list sidebar on mobile view
            $('.toggle-secondary-sidebar').click(function (e) {
                e.stopPropagation();
                $('.secondary-sidebar').toggle();
            });

            $('.split-list-toggle').click(function () {
                $('.split-list').toggleClass('slideLeft');
            });

            $('.secondary-sidebar').click(function (e) {
                e.stopPropagation();
            })
            $("*").dblclick(function (e) {
                e.preventDefault();
            });

            // Update Secondary Menu Counters every 4 seconds
            setInterval(self.updateCounters.bind(self), 4 * 1000);

            // Update unassigned rooms array every 10 seconds
            setInterval(function () {
                self.variables.state.user.getJoinableRooms()
                    .then(rooms => {
                        inbox.variables.state.unassigned = rooms;
                        console.log('Joinable Rooms', rooms);
                    })
                    .catch(err => {
                        console.log('Error getting joinable rooms: ', err);
                    })
            }, 10 * 1000);
        },

        loadMessages: function () {
            var self = this;
            self.$emailList.html('');
            console.log("Available Rooms", self.variables.state.rooms.values());
            var group = 'Today';
            var listViewGroupCont = $('<div/>', {
                "class": "list-view-group-container"
            });
            listViewGroupCont.append('<div class="list-view-group-header"><span>' + group + '</span></div>');
            var ul = $('<ul id="roomsList" class="no-padding" />', {
                "class": "no-padding"
            });
            console.log('ROOOOOOOOOM PROFILE', self.variables.state.rooms.values());
            for (let [key, element] of self.variables.state.rooms) {

                console.log('ROOOOOOOOOM PROFILE =>', element);
                var id = element.id;
                var to = element.name;
                var createdAt = element.createdAt;
                var channel = 'facebook';
                var pic = '';
                if (channel == "facebook") {
                    pic = assetsBaseUrl + '/img/profiles/fb.png';
                } else if (channel == "rcs") {
                    pic = assetsBaseUrl + '/img/profiles/rcs.png';
                } else if (channel == "web") {
                    pic = assetsBaseUrl + '/img/profiles/web.png';
                }
                else if (to.startsWith("wp")) {
                    pic = '';
                }
                var li = '<li class="item padding-15" data-email-id="' + id + '" data-email-index="' + id + '"> \
                                <div class="thumbnail-wrapper d32 circular bordered b-white"> \
                                    <img width="40" height="40" alt="" data-src-retina="' + pic + '" data-src="' + pic + '" src="' + pic + '"> \
                                </div> \
                                <div class="inline m-l-15"> \
                                    <p class="recipients no-margin hint-text small">' + to.substr(3) + '</p> \
                                    <p class="subject no-margin"></p> \
                                    <p class="body no-margin"> </p> \
                                </div> \
                                <div class="datetime timeago"><time class="timeago" datetime="' + createdAt + '">' + createdAt + '</time></div> \
                                <div class="clearfix"></div> \
                            </li>';
                ul.append(li);
            };
            listViewGroupCont.append(ul);
            self.$emailList.append(listViewGroupCont);

            //Render all dates using timeago plugin
            jQuery("time.timeago").timeago();
        },

        loadMessage: function (e, dd, self) {
            e.stopPropagation();
            e.preventDefault();
            var id = $(dd).attr('data-email-id');
            self.variables.state.current_room = id;
            console.log('data-email-d' + id);
            var email = null;
            var thumbnailWrapper = $(dd).find('.thumbnail-wrapper');
            self.variables.state.currentUser.fetchMessages({
                roomId: id,
                initialId: Number(self.variables.state.current_message_index),
                direction: 'newer',
                limit: 100,
            }).then(messages => {
                if (messages.length < 1 || messages == undefined) {
                    //empty
                    return;
                }
                if (self.variables.state.current_message_index > 0) {
                    var pic = 'http://workflow.cequens.net/bundles/cequens/img/profiles/avatar.jpg';
                    if (messages.slice(-1)[0] != undefined) {
                        pic = messages.slice(-1)[0].sender.avatarURL;
                    }
                    $('body').pgNotification({
                        style: 'circle',
                        title: '',
                        message: 'New Message ' + self.variables.state.current_room_obj.name,
                        position: 'top-right',
                        timeout: 5000,
                        type: 'info',
                        thumbnail: '<img width="40" height="40" style="display: inline-block;" src="' + pic + '" data-src="' + pic + '" data-src-retina="' + pic + '" alt="">'
                    }).show();
                }
                $('#sendMessage').css("display", "");
                if (messages.slice(-1)[0] != undefined) {
                    self.variables.state.current_message_index = messages.slice(-1)[0].id;
                }
                var messagesArray = {
                    'messages': [],
                    'me': self.variables.state.user.id,
                    'room': self.variables.state.current_room_obj
                };
                $.each(messages, function (j) {
                    var messageObj = messages[j];
                    self.variables.state.current_room_users[messageObj.sender.id] = messageObj.sender;
                    var messageText = JSON.parse(messageObj.text);
                    messageObj.text = messageText;
                    messagesArray.messages.push(messageObj);
                });
                console.log('Messages ', messagesArray);
                var tmpl_name = 'script#template-timelineBlock';
                var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                var gg = tmpl_module_setting(messagesArray);
                self.$emailOpened.find('.chat-view').find('.chat-inner').append(tmpl_module_setting(messagesArray));
                var tmpl_name = 'script#template-userInfoBlock';
                var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                var userId = self.variables.state.current_room_obj.name.substr(3);
                console.log('UserDetailsssssss => ', self.variables.state.current_room_users[userId]);
                var clientDetailsArray = {
                    'user': self.variables.state.current_room_users[userId],
                    'room': self.variables.state.current_room_obj
                };
                $('#client_details').html(tmpl_module_setting(clientDetailsArray));
                var thumbnailClasses = thumbnailWrapper.attr('class').replace('d32', 'd48');
                self.$emailOpened.find('.thumbnail-wrapper').html(thumbnailWrapper.html()).attr('class', thumbnailClasses);

                $('.no-result').hide();
                $('.actions-dropdown').toggle();
                $('.actions, .email-content-wrapper').show();
                $('#chat').show();
                if ($.Pages.isVisibleSm() || $.Pages.isVisibleXs()) {
                    $('.split-list').toggleClass('slideLeft');
                }
                $('.chat-inner').stop().animate({
                    scrollTop: $('.chat-inner')[0].scrollHeight
                }, 800);

                // Initialize message action menu
                $('.menuclipper').menuclipper({
                    bufferWidth: 20
                });
                $('.item').removeClass('active');
                $(dd).addClass('active');
                jQuery("time.timeago").timeago();

            }).catch(err => {
                console.log(`Error fetching messages: ${err}`);
            });


        },

        addNewMessage: function (message) {
            var self = this;
            console.log('llllllllllll');
            self.variables.state.current_room = message.room.id;
            console.log('data-email-d' + message.room.id);
            var email = null;
            var thumbnailWrapper = $('.thumbnail-wrapper');

            if (message == undefined) {
                //empty
                return;
            }
            if (self.variables.state.current_message_index > 0) {
                var pic = 'http://workflow.cequens.net/bundles/cequens/img/profiles/avatar.jpg';
                if (message != undefined) {
                    pic = message.sender.avatarURL;
                }
                //$('body').pgNotification({
                //		style: 'simple',
                //		message: 'New Message Received From '+self.variables.state.current_room_obj.name,
                //		position: 'top-right',
                //		timeout: 0,
                //		type: 'info'
                //	}).show();

                $('body').pgNotification({
                    style: 'circle',
                    title: '',
                    message: 'New Message ' + self.variables.state.current_room_obj.name,
                    position: 'top-right',
                    timeout: 0,
                    type: 'info',
                    thumbnail: '<img width="40" height="40" style="display: inline-block;" src="' + pic + '" data-src="' + pic + '" data-src-retina="' + pic + '" alt="">'
                }).show();
            }
            $('#sendMessage').css("display", "");
            // do something with the messages
            if (message != undefined) {
                self.variables.state.current_message_index = message.id;
            }
            var messagesArray = {
                'messages': [],
                'me': self.variables.state.user.id,
                'room': self.variables.state.current_room_obj
            };

            var messageObj = message;
            self.variables.state.current_room_users[messageObj.sender.id] = messageObj.sender;
            var messageText = JSON.parse(messageObj.text);
            messageObj.text = messageText;
            messagesArray.messages.push(messageObj);

            console.log('Messages ', messagesArray);
            var tmpl_name = 'script#template-timelineBlock';
            var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
            var gg = tmpl_module_setting(messagesArray);
            //self.$emailOpened.find('.email-content').find('.timeline-container').find('.timeline').append(tmpl_module_setting(messagesArray));
            self.$emailOpened.find('.chat-view').find('.chat-inner').append(tmpl_module_setting(messagesArray));
            var tmpl_name = 'script#template-userInfoBlock';
            var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
            var userId = self.variables.state.current_room_obj.name.substr(3);
            console.log('UserDetailsssssss => ', self.variables.state.current_room_users[userId]);
            var clientDetailsArray = {
                'user': self.variables.state.current_room_users[userId],
                'room': self.variables.state.current_room_obj
            };
            $('#client_details').html(tmpl_module_setting(clientDetailsArray));
            //self.$emailOpened.find('#sendMessageFromAgent').find('#bot_id').val('333');
            //self.$emailOpened.find('#sendMessageFromAgent').find('#user_id').val('ddd');

            //self.$emailOpened.find('.sender .name').text(message.user_id);
            //self.$emailOpened.find('.sender .datetime').text(message.created_at);
            //self.$emailOpened.find('.subject').text('');
            //self.$emailOpened.find('.email-content-body').html(message.text);

            var thumbnailClasses = thumbnailWrapper.attr('class').replace('d32', 'd48');
            self.$emailOpened.find('.thumbnail-wrapper').html(thumbnailWrapper.html()).attr('class', thumbnailClasses);

            $('.no-result').hide();
            $('.actions-dropdown').toggle();
            $('.actions, .email-content-wrapper').show();
            $('#chat').show();
            if ($.Pages.isVisibleSm() || $.Pages.isVisibleXs()) {
                $('.split-list').toggleClass('slideLeft');
            }

            //!$('.email-reply').data('wysihtml5') && $('.email-reply').wysihtml5(editorOptions);

            //$(".email-contnt-wrapper-scroller").scrollTop(0);
            $('.chat-inner').stop().animate({
                scrollTop: $('.chat-inner')[0].scrollHeight
            }, 800);

            // Initialize email action menu
            $('.menuclipper').menuclipper({
                bufferWidth: 20
            });

            jQuery("time.timeago").timeago();

            //setInterval(fn60sec, 60*1000);

        },

        addNewRoom: function (room) {
            inbox.variables.state.rooms.set(room.id + '', room);
            inbox.variables.state.assigned.push(room);
            var ulRooms = $('#roomsList');
            console.log('Adding New Room => ', room);
            var id = room.id;
            var to = room.name;
            var createdAt = room.createdAt;
            var pic = '';
            var channel = room.customData.channel_type;
            if (channel == "facebook") {
                pic = assetsBaseUrl + '/img/profiles/fb.png';
            } else if (channel == "rcs") {
                pic = assetsBaseUrl + '/img/profiles/rcs.png';
            }  else if (channel == "web") {
                pic = assetsBaseUrl + '/img/profiles/web.png';
            }
            else if (to.startsWith("wp")) {
                pic = '';
            }
            var li = '<li class="item padding-15" data-email-id="' + id + '" data-email-index="' + id + '"> \
                                <div class="thumbnail-wrapper d32 circular bordered b-white"> \
                                    <img width="40" height="40" alt="" data-src-retina="' + pic + '" data-src="' + pic + '" src="' + pic + '"> \
                                </div> \
                                <div class="inline m-l-15"> \
                                    <p class="recipients no-margin hint-text small">' + to.substr(3) + '</p> \
                                    <p class="subject no-margin"></p> \
                                    <p class="body no-margin"> </p> \
                                </div> \
                                <div class="datetime"><time class="timeago" datetime="' + createdAt + '">' + createdAt + '</time></div> \
                                <div class="clearfix"></div> \
                            </li>';
            $(li).appendTo(ulRooms).slideDown('slow');
            //inbox.bindListEvent();
            $("time.timeago").timeago();
        },

        removeRoom: function (room) {
            var ulRooms = $('#roomsList');
            console.log('Removing Room => ', room);
            inbox.variables.state.rooms.delete(room.id);
            $.each(inbox.variables.state.assigned, function (j) {
                var roomObj = inbox.variables.state.assigned[j];
                if (roomObj.id == room.id) {
                    inbox.variables.state.assigned.splice(j, 1)
                }
            });
            //inbox.variables.state.currentUser.roomSubscriptions[room.id].cancel();
            ulRooms.find('*[data-email-id="' + room.id + '"]').slideUp('slow', function () {
                ulRooms.find('*[data-email-id="' + room.id + '"]').remove();
            });
        },

        updateCounters: function () {
            var self = this;
            var assignedCounter = self.variables.state.assigned.length;
            var unassigned_counter = self.variables.state.unassigned.length;
            $('.assigned_counter').text(assignedCounter);
            $('.unassigned_counter').text(unassigned_counter);
        },

    };

    chatManager.connect({
        onAddedToRoom: room => {
            console.log(`Added to room ${room.name}`);
            inbox.addNewRoom(room);
        },
        onRemovedFromRoom: room => {
            console.log(`Added to room ${room.name}`)
        },
        onRoomDeleted: room => {
            console.log(`Remove room ${room.name}`)
            inbox.removeRoom(room);
        }
    }).then(
        currentUser => {
            currentUserObj = currentUser;
            inbox.variables.state.user = currentUser;
            inbox.variables.state.assigned = inbox.variables.state.user.rooms;
            currentUser.getJoinableRooms()
                .then(rooms => {
                    inbox.variables.state.unassigned = rooms;
                    console.log('Joinable Rooms', rooms);
                    inbox.init();

                })
                .catch(err => {
                    console.log('Error getting joinable rooms: ', err);
                })
        }
    ).catch(error => {
        console.error("error:", error);
    });

    $(window).resize(function () {

        if ($(window).width() <= 1024) {
            $('.secondary-sidebar').hide();

        } else {
            $('.split-list').length && $('.split-list').removeClass('slideLeft');
            $('.secondary-sidebar').show();
        }
    });
});