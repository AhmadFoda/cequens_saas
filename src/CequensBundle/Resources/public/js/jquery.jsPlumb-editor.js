var jsPlumbEditor = (jsPlumbEditor || {});
(function( $ ){
	var modalModuleElem = $('#modalSlideLeft');
	var modalTestFlow = $('#modalSlideLeftTest');
    var methods = {
        //Initialize plugin
        init : function( options ) {
            return this.each(function(){
				// compare helper for comparison operators.
				Handlebars.registerHelper('compare', function(lvalue, rvalue, options) {

					if (arguments.length < 3)
						throw new Error("Handlerbars Helper 'compare' needs 2 parameters");

					operator = options.hash.operator || "==";

					var operators = {
						'==':       function(l,r) { return l == r; },
						'===':      function(l,r) { return l === r; },
						'!=':       function(l,r) { return l != r; },
						'<':        function(l,r) { return l < r; },
						'>':        function(l,r) { return l > r; },
						'<=':       function(l,r) { return l <= r; },
						'>=':       function(l,r) { return l >= r; },
						'&&':       function(l,r) { return l && r; },
						'||':       function(l,r) { return l || r; },
						'typeof':   function(l,r) { return typeof l == r; }
					};

					if (!operators[operator])
						throw new Error("Handlerbars Helper 'compare' doesn't know the operator "+operator);

					var result = operators[operator](lvalue,rvalue);

					if( result ) {
						return options.fn(this);
					} else {
						return options.inverse(this);
					}
				});

				// isIn helper for checking if an element is present in an array.
				Handlebars.registerHelper('isIn', function(element, array, options) {
					if (!array || !element) return options.inverse(this);

					for (var i = 0; i < array.length; i++) {
						if (element == array[i])
							return options.fn(this);
					}
					return options.inverse(this);
				});
				var jsplumb_instance;
                var self = $(this);
                var data = self.data('jsplumb_editor');
                // If the plugin hasn't been initialized yet
                if(!data){
                    data = methods.setData.call(this, self, options);
                    self.data('jsplumb_editor', data);
                }
                jsplumb_instance = data.jsplumb_instance;
                methods.renderEditor.call(this, self, jsplumb_instance, data);
                if(data.options.autoload){
                    methods.load.call(this, data.options);
                }
            });
        },
        //Destroy plugin
        destroy : function( ) {
            return this.each(function(){
                var self = $(this);
                var data = self.data('jsplumb_editor');
                $(window).unbind('.jsplumb_editor');
                data.jsplumb_editor.remove();
                self.removeData('jsplumb_editor');
            })
        },
        //Setup plugin options
        setOptions: function(options){
            var defaults = {
                autoload: true,
                modules: {},
                wireLabel: function(){
                    return {}
                },
                buttons: {
                    save: {
                        label: "Save",
                        action: function(e, editor){
                            methods.save.call(editor.get(0), editor);
                        },
                        icon: "database_save"
                    },
                    clear: {
                        label: "Clear",
                        action: function(e, editor){
                            methods.clear.call(editor.get(0), editor);
                        },
                        icon: "page_white_delete"
						},
					testFlow: {
						label: "Test Flow",
						action: function (e, editor) {
							methods.testFlow.call(editor.get(0), editor);
						}
					},
                    sort: {
                        label: "Organize",
                        action: function(e, editor){
                            methods.sort.call(editor.get(0), editor);
                        },
                        icon: "shape_move_back "
                    },
                    /*	reload: {
                            label: "Reload",
                            action: function(e, editor){
                                methods.reload.call(editor.get(0), editor);
                            },
                            icon: "arrow_refresh"
                        },

                    */
                }
            };
            options = $.extend(true, {}, defaults, options || {});
        
            return options;
        },
        //Setup plugin options
        setData: function(self, options){
            var data;
            options = methods.setOptions.call(this, options);
            data = {
                container : this,
                jsplumb_editor : self,
                jsplumb_instance: jsPlumb.getInstance(),
                options: options,
                modules: options.modules,
                buttons: options.buttons,
                containers: [],
                connections: [],
                containerDetails:new Array(),
                connectionDetails:new Array()
            };
            return data;
        },
        //Render the editor inside the provided container
        renderEditor: function(self, jsplumb_instance, data){
            var menubar = $("<div></div>");
            var toolbar = $("<div></div>");
            var canvas = $("<div></div>");
        
            self.addClass("jsPlumb-editor");
            toolbar.addClass("jsPlumb-editor-toolbar");
            canvas.addClass("jsPlumb-editor-canvas");
            menubar.addClass("jsPlumb-editor-menubar");
        
            data.canvas = canvas;
            data.toolbar = toolbar;
            data.menubar = menubar;
        
            self.prepend(menubar);
            self.append(toolbar);
            self.append(canvas);
            
            methods.renderMenuItems.call(self, menubar, data);
            methods.renderModules.call(self, toolbar, data);
            methods.initializeJsPlumb.call(self, jsplumb_instance, canvas, data);
            methods.initializeSpringy.call(self, data);
            
            toolbar.find(".jsPlumb-editor-module").each(function(i, module){
                $(module).draggable({
                    helper: "clone"
                });
            });
            
            canvas.droppable({
                accept: ".jsPlumb-editor-module",
                drop: function(event, ui){
                    var moduleEl = $(ui.draggable);
                    var moduleKey = moduleEl.attr("data-module-key");
                    if(moduleKey && (!moduleEl.hasClass("jsplumb-editor-container"))){
                        var position = ui.position;
                        var offset = canvas.position();
                        //Correct drop window position vs canvas position
                        position.left -= offset.left;
                        position.top -= offset.top;
                        //Add container to canvas
                        methods.addContainer.call(self, {
                            module: moduleKey,
                            position: position
                        });
                    }
                }
            });
            
            
        },
        //Render the modules list
        renderModules: function(toolbar, data){
            var modules = data.modules;
            var title = $("<div>Steps</div>");
            var list = $("<ul class='jsPlumb-editor-toolbar-list'></ul>");
            var divI = $('<div class=\'palettes\'></div>');
            title.addClass("jsPlumb-editor-toolbar-title");

            //toolbar.append(title);
            //toolbar.append(divI);
            toolbar.append(list);
            for(var key in modules){
                methods.renderModule.call(this, list, modules[key], key);
            }
        },
        //Render a module list item
        renderModule: function(list, moduleOptions, key){
            var moduleEl = $("<div></div>");
            var listItemEl = $("<li></li>");
            var defaults = {
                visibleLabel: false,
                label: ""
            }
            moduleOptions = $.extend(true, {}, defaults, moduleOptions || {});
            listItemEl.addClass("jsPlumb-editor-toolbar-item");
            moduleEl.addClass("jsPlumb-editor-module");
            moduleEl.attr("title", moduleOptions.label);
            moduleEl.attr("data-module-key", key);
			moduleEl.append("<div class=\"text-area\"><div class=\"component-info\"><img src=\""+assetsBaseUrl+moduleOptions.image+"\"> <span class=\"__comp-name\">"+moduleOptions.label+"</span></div></div>");

            // if(moduleOptions.image){
            //     moduleEl.append("<img src=\""+moduleOptions.image+"\">");
            // }
            //
            // if(moduleOptions.visibleLabel){
            //     moduleEl.append("<label>"+moduleOptions.label+"</label>");
            // }
            
            listItemEl.append(moduleEl);
            list.append(listItemEl);
        },
        //Render the modules list
        renderMenuItems: function(menubar, data){
            var buttons = data.buttons;
            var list = $("<ul></ul>");
            list.addClass("jsPlumb-editor-menubar-list");
            menubar.append(list);
            for(var key in buttons){
                methods.renderMenuItem.call(this, list, buttons[key], key);
            }
        },
        //Render a module list item
        renderMenuItem: function(list, buttonOptions, key){
            var self = $(this);
            var buttonEl = $("<button></button>");
            var listItemEl = $("<li></li>");
            var defaults = {
                label: "",
                action: function(){},
                css: "btn btn-danger"
            }
            buttonOptions = $.extend(true, {}, defaults, buttonOptions || {});
            listItemEl.addClass("jsPlumb-editor-menubar-item");
            buttonEl.addClass("jsPlumb-editor-menu-buttonn");
            buttonEl.addClass(buttonOptions.css);
            buttonEl.attr("title", buttonOptions.label);
            buttonEl.append(buttonOptions.label);
            buttonEl.click(function(e){
                stopPropagation(e);
                buttonOptions.action.call(this, e, self);
            });
            if(buttonOptions.icon){
                var icon = $("<span></span>");
                icon.addClass("ss_sprite ss_"+buttonOptions.icon);
                buttonEl.prepend(icon);
            }
            
            listItemEl.append(buttonEl);
            list.append(listItemEl);
        },
		//Render module Details
		renderMenuModuleDetails: function(moduleId,devId,dd,data){
			var self = $(this);
			console.log(data.containerDetails);
			var modalElem = modalModuleElem;
			var ddd=$('#'+devId);

            console.log('Loading Details for container index => ',dd);
            var container = data.containers[dd];
            console.log('Container Details => ',container);
            /*var data_ = {
				step_name: '[Step Name]',
				type_name: dd.module,
				module_id: 20,
				is_required: true
			};*/
			console.log('before ajax ',moduleId);
			console.log('already saved container details ',data.containerDetails);
			modalElem.find('.js_module_details').html('');
			var steps_container = modalElem.find('.modal-body');
			if(container.metadata.instance_id==undefined)
            {
                $.ajax({
                    type: 'GET',
                    async:false,
                    dataType: 'json',
                    url: applicationBaseUrl+'/portal/workflow/modules/'+moduleId+'/settings',
                    data: {},
                    success: function(settings) {
                        //loading_modules_ajax--;
                        //el.find('.js-step-settings-container').html('');
                        var step_attributes = {
                            id: moduleId,
                            name: 'll',
                            settings: []
                        };
                        if (settings.length > 0) {

                            $.each(settings, function(index, setting_attributes) {
                                var tmpl_name = 'script#template-moduleSettings' + setting_attributes.type.name;
                                if ($(tmpl_name).length > 0) {
                                    var tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                                    modalElem.find('.js_module_details').append(tmpl_module_setting(setting_attributes));
                                };
                            });
                        }
                        modalElem.find('#js-btn-save-module-details').unbind('click');
                        modalElem.find('#js-btn-save-module-details').click(function(e){e.preventDefault();}).click(function(e){
                            e.preventDefault();
                            var self = $(this);

                            // Prevent double post-back.
                            if (self.hasClass('disabled')) {
                                return false;
                            }

                            //var Input = modalElem.find('.js_module_connection_details').find('#js-module-connection-input').val();
                            //var InputId = modalElem.find('.js_module_connection_details').find('#connectionId').val();
                            //console.log('InputId:'+InputId);
                            //console.log('InputValue'+Input);

                            // Form Validation.
                            modalElem.find('.js_module_details').find('div.form-group.has-error').removeClass('has-error');
                            var required_fields = modalElem.find('.js_module_details').find('.js-required-field');
                            if (required_fields.length) {
                                $.each(required_fields, function(index, item) {
                                    var required_field = $(item);
                                    if (required_field.val() == null || required_field.val() == '0' || required_field.val() == '') {
                                        required_field.parents('div.form-group').addClass('has-error');
                                    }
                                });
                            }

                            // Move focus to first invalid field.
                            if (modalElem.find('.js_module_details').find('div.form-group.has-error').length) {
                                modalElem.find('.js_module_details').find('div.form-group.has-error').find('input, select, textarea').first().focus();
                                return false;
                            }

                            // Loop over settings for each module instance.
                            $.each(modalElem.find('.js_module_details').find('.js-setting-row'), function(index, setting_row) {
                                setting_row = $(setting_row);
                                var settings = {
                                    id: setting_row.find('input.js-setting-type-id').val(),
                                    attributes: []
                                };

                                // Get list of values for values list.
                                if (setting_row.hasClass('js-setting-values-row')) {
                                    $.each(setting_row.find('.js-setting-dynamic-row input'), function(i, setting_row_field) {
                                        settings['attributes'].push({
                                            value: setting_row_field.value
                                        });
                                    });

                                } else if (setting_row.hasClass('js-setting-keyvalues-row')) {
                                    $.each(setting_row.find('.js-setting-dynamic-row'), function(i, setting_row) {
                                        settings['attributes'].push({
                                            key: $(setting_row).find('input').eq(0).val(),
                                            value: $(setting_row).find('input').eq(1).val()
                                        });
                                    });
                                } else if (setting_row.hasClass('js-setting-keyvalues-workflow-row')) {
                                    $.each(setting_row.find('.js-setting-dynamic-row'), function(i, setting_row) {
                                        settings['attributes'].push({
                                            key: $(setting_row).find('input').eq(0).val(),
                                            value: $(setting_row).find(':selected').val()
                                        });
                                    });
                                } else {
                                    settings['attributes'].push({
                                        value: setting_row.find('input.form-control, select.form-control').val()
                                    });
                                }
                                step_attributes.settings.push(settings);
                            });
                            console.log('savedModuleDetails',step_attributes);
                            container.metadata.settings=step_attributes.settings;
                            data.containerDetails[devId] = step_attributes;
                            //connection.setLabel(Input);
                            //$('#modalSlideLeftConnection').modal('hide');
                            modalElem.find('.js_module_details').html('');
                            modalElem.modal( 'hide' ).data( 'bs.modal', null );
                        });
                        modalElem.modal( 'show' );

                    },
                    error: function(response) {

                    }
                });
            }
			else
            {
                console.log('Container Details already loaded from backend');
                var settings = container.metadata.settings;
                var settingsStructure = [];
                var step_attributes = {
                    id: moduleId,
                    name: 'll',
                    settings: settings
                };
                for (var settingId in settings) {
                    var attribute = settings[settingId];
                    var tmpl_name = 'script#template-moduleSettings' + attribute.type.name;
                    var tmpl_name_setting = 'script#template-moduleSetting' + attribute.type.name;
                    var tmpl_module_setting;
                    var tmpl_module_settingg;
                    if ($(tmpl_name).length > 0) {

                        var data_ = {
                            id: parseInt(attribute.id),
                            label: attribute.display_name,
                            is_required: attribute.is_required,
                            options: attribute.options
                        };

                        if (attribute.type.name == 'NameValueArray') {
                            tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                            var split = attribute.value[0].split(":");
                            data.key = split[0];
                            data.value = split[1];
                            tmpl_module_settingg = Handlebars.compile($('script#template-moduleSettingKeyValue').html());
                            modalElem.find('.js_module_details').append(tmpl_module_setting(data_));
                            for (var j = 0; j < attribute.value.length; j++) {
                                var split = attribute.value[j].split(":");
                                modalElem.find('.js_module_details').find('#settings_'+attribute.id).find('.js-more-key-values').append(tmpl_module_settingg({
                                    is_required: true,
                                    key: split[0],
                                    value: split[1]
                                }));
                            }


                            //var split = attribute.value[0].split(":");
                            //data_.key = split[0];
                            //data_.value = split[1];
                        }
                        else if (attribute.type.name == 'Multiselect') {
                            data_.value = attribute.value;
                            tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                            modalElem.find('.js_module_details').append(tmpl_module_setting(data_));
                        }
                        else if (attribute.type.name == 'NameValueArrayWorkflow') {
                            var split = attribute.value[0].split(":");
                            data_.key = split[0];
                            data_.value = split[1];
                            tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                            modalElem.find('.js_module_details').append(tmpl_module_setting(data_));
                        }
                        else {
                            data_.value = attribute.value[0];
                            tmpl_module_setting = Handlebars.compile($(tmpl_name).html());
                            modalElem.find('.js_module_details').append(tmpl_module_setting(data_));
                        }

                        if(attribute.type.name == 'NameValueArrayWorkflow')
                        {

                        }
                        else
                        {

                        }


                    };
                }
                modalElem.find('#js-btn-save-module-details').unbind('click');
                modalElem.find('#js-btn-save-module-details').click(function(e){e.preventDefault();}).click(function(e){
                    e.preventDefault();
                    var self = $(this);

                    // Prevent double post-back.
                    if (self.hasClass('disabled')) {
                        return false;
                    }

                    //var Input = modalElem.find('.js_module_connection_details').find('#js-module-connection-input').val();
                    //var InputId = modalElem.find('.js_module_connection_details').find('#connectionId').val();
                    //console.log('InputId:'+InputId);
                    //console.log('InputValue'+Input);

                    // Form Validation.
                    modalElem.find('.js_module_details').find('div.form-group.has-error').removeClass('has-error');
                    var required_fields = modalElem.find('.js_module_details').find('.js-required-field');
                    if (required_fields.length) {
                        $.each(required_fields, function(index, item) {
                            var required_field = $(item);
                            if (required_field.val() == null || required_field.val() == '0' || required_field.val() == '') {
                                required_field.parents('div.form-group').addClass('has-error');
                            }
                        });
                    }

                    // Move focus to first invalid field.
                    if (modalElem.find('.js_module_details').find('div.form-group.has-error').length) {
                        modalElem.find('.js_module_details').find('div.form-group.has-error').find('input, select, textarea').first().focus();
                        return false;
                    }

                    // Loop over settings for each module instance.
                    step_attributes.settings = [];
                    $.each(modalElem.find('.js_module_details').find('.js-setting-row'), function(index, setting_row) {
                        setting_row = $(setting_row);
                        var settings = {
                            id: parseInt(setting_row.find('input.js-setting-type-id').val()),
                            options: [],
                            type:undefined,
                            value: [],
                            attributes: []
                        };

                        for (var containerMetadataSettingId in container.metadata.settings) {
                            var dfdf = container.metadata.settings[containerMetadataSettingId];
                            if(dfdf.id==settings.id)
                            {
                                settings.type = dfdf.type;
                                settings.options = dfdf.options;
                            }
                        }

                        // Get list of values for values list.
                        if (setting_row.hasClass('js-setting-values-row')) {
                            $.each(setting_row.find('.js-setting-dynamic-row input'), function(i, setting_row_field) {
                                settings['attributes'].push({
                                    value: setting_row_field.value
                                });
                                settings['value'].push(setting_row_field.value);
                            });

                        } else if (setting_row.hasClass('js-setting-keyvalues-row')) {
                            $.each(setting_row.find('.js-setting-dynamic-row'), function(i, setting_row) {
                                settings['attributes'].push({
                                    key: $(setting_row).find('input').eq(0).val(),
                                    value: $(setting_row).find('input').eq(1).val()
                                });
                                settings['value'].push($(setting_row).find('input').eq(0).val()+":"+  $(setting_row).find('input').eq(1).val());
                            });
                        } else if (setting_row.hasClass('js-setting-keyvalues-workflow-row')) {
                            $.each(setting_row.find('.js-setting-dynamic-row'), function(i, setting_row) {
                                settings['attributes'].push({
                                    key: $(setting_row).find('input').eq(0).val(),
                                    value: $(setting_row).find(':selected').val()
                                });
                                settings['value'].push($(setting_row).find('input').eq(0).val()+":"+ $(setting_row).find(':selected').val());
                            });
                        } else {
                            settings['attributes'].push({
                                value: setting_row.find('input.form-control, select.form-control').val()
                            });
                            settings['value'].push(setting_row.find('input.form-control, select.form-control').val());
                        }
                        step_attributes.settings.push(settings);
                    });
                    console.log('savedModuleDetails',step_attributes);
                    container.metadata.settings = [];
                    container.metadata.settings = step_attributes.settings;
                    console.log('Adddddddddding Container Detailssssss to ',data.containerDetails);
                    data.containerDetails[devId] = [];
                    data.containerDetails[devId] = step_attributes;
                    //connection.setLabel(Input);
                    //$('#modalSlideLeftConnection').modal('hide');
                    modalElem.find('.js_module_details').html('');
                    modalElem.modal( 'hide' ).data( 'bs.modal', null );
                });
                modalElem.modal( 'show' );

            }



			//modalElem.find('.js_module_details').html('Module is '+dd.module+' Div is'+devId);
		},
		//Render Module Connection Details
		renderMenuModuleConnectionDetails: function(connection,JsInstance,dataa){
			var modalElem = $('#modalSlideLeftConnection');
			var srcId = connection.sourceId;
			var targetId = connection.targetId;
			var source = $("#"+connection.sourceId);
			var target = $("#"+connection.targetId);
			var sourceType = source.data("jsplumb_container").module;
			var targetType = target.data("jsplumb_container").module;
			console.log('Connection From '+sourceType+' To '+targetType);
			console.log('Connection =>',connection);
			var context = {sourceType: sourceType, targetType: targetType};
			modalElem.find('.js_module_connection_details').html('');
			modalElem.find('.js_module_connection_details').append('<input id="connectionId" value="'+connection.id+'" type="hidden"/>');
			var tmpl_name = 'script#template-connectionInstanceNoInput';
			if(sourceType=='menu')
			{
				tmpl_name = 'script#template-connectionInstanceInput';
			}
			else if(sourceType=='branch')
            {
                tmpl_name = 'script#template-connectionInstanceBranch';
            }
			var tmpl_module_connection_setting = Handlebars.compile($(tmpl_name).html());
			modalElem.find('.js_module_connection_details').append(tmpl_module_connection_setting(context));
			console.log('DataaaConnectionDetails',dataa.connectionDetails);
			if(dataa.connectionDetails[connection.id]!=undefined)
			{
				modalElem.find('.js_module_connection_details').find('#js-module-connection-input').val(dataa.connectionDetails[connection.id].inputValue);
			}

			modalElem.find('.js_module_connection_details').append('<br/><input type="button" class="btn btn-danger" id="js-delete-connection-modal" value="Delete Connection">');
			modalElem.find('.js_module_connection_details').find('#js-delete-connection-modal').click(function(e){
				JsInstance.detach(connection);
				$('#modalSlideLeftConnection').modal('hide');
			});
			modalElem.find('#js-btn-save-connection-details').click(function(e){
				var Input = modalElem.find('.js_module_connection_details').find('#js-module-connection-input').val();
				var InputId = modalElem.find('.js_module_connection_details').find('#connectionId').val();
				console.log('InputId:'+InputId);
				console.log('InputValue'+Input);
				dataa.connectionDetails[InputId] = {inputValue:Input};
				console.log(dataa.connections);
				dataa.connections.forEach(function (arrayItem) {
					if(arrayItem.connection.id==InputId)
					{
						arrayItem.connection.setLabel(Input);
					}
				});

				//connection.setLabel(Input);
				//$('#modalSlideLeftConnection').modal('hide');
				$( '#modalSlideLeftConnection' ).modal( 'hide' ).data( 'bs.modal', null );
				});
			//modalElem.find('.js_module_details').html('Module is '+dd.module+' Div is'+devId);
			$('#modalSlideLeftConnection').modal('show');
		},
        //Initialize the jsplumb component
        initializeJsPlumb: function(jsplumb_instance, canvas, data){
            var self = this;
            var wireLabel = data.options.wireLabel;
            var curColourIndex = 1, maxColourIndex = 24, nextColour = function() {
                var R,G,B;
                R = parseInt(128+Math.sin((curColourIndex*3+0)*1.3)*128);
                G = parseInt(128+Math.sin((curColourIndex*3+1)*1.3)*128);
                B = parseInt(128+Math.sin((curColourIndex*3+2)*1.3)*128);
                curColourIndex = curColourIndex + 1;
                if (curColourIndex > maxColourIndex) curColourIndex = 1;
                return "rgb(" + R + "," + G + "," + B + ")";
            };
        
            jsplumb_instance.bind("jsPlumbConnection", function(eventData) {
                eventData.connection.setPaintStyle({
                    strokeStyle:nextColour()
                });
                console.log('jsPlumbConnectionEvent=>',eventData);
                eventData.connection.getOverlay("label").setLabel(eventData.connection.id);
            });
        
            //Validate connection
            jsplumb_instance.bind("beforeDrop",function(eventData) {
                var result = false;
                var source = $("#"+eventData.sourceId);
                var target = $("#"+eventData.targetId);
                var sourceAllowedIn = data.options.modules[source.data("jsplumb_container").module].allowedConnectionsIn;
				var sourceAllowedOut = data.options.modules[source.data("jsplumb_container").module].allowedConnectionsOut;
				var targetAllowedIn = data.options.modules[target.data("jsplumb_container").module].allowedConnectionsIn;
				var targetAllowedOut = data.options.modules[target.data("jsplumb_container").module].allowedConnectionsOut;
				var sourceType = source.data("jsplumb_container").module;
				var targetType = target.data("jsplumb_container").module;
				var acceptedTypes = target.data("jsplumb_container").acceptedConnections;
				var sourceTotalAvailableConn = jsplumb_instance.select({source:eventData.sourceId}).length;
				var targetTotalAvailableConn = jsplumb_instance.select({source:eventData.targetId}).length;
				console.log('dropping from '+eventData.sourceId+' to '+eventData.targetId);
				console.log('SourceType '+sourceType+' TargetType '+targetType);
                if((acceptedTypes == "all")||(acceptedTypes.indexOf(sourceType) > -1)){
                    result = true;
                }
                else{
                    result = false;
                }

                //Restrict out connections if more than allowed limit
				if(sourceTotalAvailableConn == sourceAllowedOut)
				{
					result =  false;
				}

                return result;
            });
            if(wireLabel){
                jsplumb_instance.bind("jsPlumbConnection", function(info) {
                    var connection = info.connection;
                    var source = info.source;
                    var target = info.target;
                    var wireLabelOptions;
                    if(typeof wireLabel == "function"){
                        wireLabelOptions = wireLabel.call(this, info, source.data("jsplumb_container"), target.data("jsplumb_container"));
                    }
                    else{
                        wireLabelOptions = wireLabel;
                    }
                    if(wireLabelOptions){
                        wireLabelOptions.cssClass = "jsplumb-editor-connection-label " + (wireLabelOptions.cssClass || "")
                        connection.addOverlay([ "Label", wireLabelOptions]);
                    }
                });
            }
        
            //Register connection
            jsplumb_instance.bind("jsPlumbConnection", function(eventData) {
                methods._connectionAdded.call(self, eventData);
            });
        
            //Un-register connection
            jsplumb_instance.bind("jsPlumbConnectionDetached", function(eventData) {
                methods._connectionRemoved.call(self, eventData);
            });
        
        
            jsplumb_instance.importDefaults({
                Endpoint : ["Dot", {
                    radius:2
                }],
                HoverPaintStyle : {
                    strokeStyle:"#42a62c", 
                    lineWidth:1
                },
                ConnectionOverlays : [
                [ "Arrow", { 
                    location:1,
                    id:"arrow",
                    length:10,
                    foldback:0.8
                }]
                ]
            });
            
            // bind a click listener to each connection; the connection is deleted.
            jsplumb_instance.bind("dblclick", function(connection) {
                //alert('Double Click Connection');
				//var ff = {connection:connection,jsInst:jsplumb_instance};
				methods.renderMenuModuleConnectionDetails(connection,jsplumb_instance,data);
                //jsplumb_instance.detach(connection);
            });

            // Handle adding new value field.
            modalModuleElem.on('click', 'a.js-add-value', function(e) {
                e.preventDefault();
                var tmpl_module_setting_value = Handlebars.compile($('script#template-moduleSettingValue').html());
                $(this).parents('.js-setting-row').find('.js-more-values').append(tmpl_module_setting_value());
            });

            // Handle adding new value field.
            modalModuleElem.on('click', 'a.js-add-key-value', function(e) {
                e.preventDefault();
                var tmpl_module_setting_keyvalue = Handlebars.compile($('script#template-moduleSettingKeyValue').html());
                $(this).parents('.js-setting-row').find('.js-more-key-values').append(tmpl_module_setting_keyvalue());
            });


            modalModuleElem.on('click', 'a.js-remove-setting-row', function(e) {
                e.preventDefault();
                var row_container = $(this).parents('div.js-setting-dynamic-row');
                row_container.fadeOut(250, function() {
                    row_container.remove();
                });
            });

        },
        //Load json data
        loadJson: function(json){
            var connections, containers;
            var self = $(this);
            json = ((typeof json == "object") ? json : {});
            
            connections = json.connections || [];
            containers = json.containers || [];
            console.log('containerssss',containers);
            console.log('connectionsssss',connections);
			for(var index = 0; index < containers.length; index++){
                methods.addContainer.call(this, containers[index]);
            }

            for(var index = 0; index < connections.length; index++){
            	console.log('addingConnectionnnnn',containers[index]);
                methods.addConnection.call(this, connections[index]);
            }
        },
        //Add a new container
        addContainer: function(containerData){
            //Add Container Karim
            //alert('add container on canvas');
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var canvas = data.canvas;
            var rejectContainer = false;
			console.log('ContainerData',containerData);

			$.each(data.containers, function( key, value ) {
				if(value.module==containerData.module)
				{
					//console.log('Reject Container');
					//rejectContainer = true;
				}
			    console.log('caste: ' + value.module );
			});
			if(rejectContainer)
            {
                return false;
            }

            var jsplumb_instance = data.jsplumb_instance;
            var moduleOptions = data.modules[containerData.module];
            var containerOptions = $.extend(true, {}, moduleOptions, containerData);
            containerOptions.metadata = {'instance_id':containerData.instance_id,'settings':containerData.settings};
            var container = new jsPlumbEditor.Container(containerOptions, canvas, jsplumb_instance, self, methods);
            console.log('addContainer',data.containers);
            container.el.bind("jsplumb-remove-container", function(event, deletedContainer){
                methods.removeContainer.call(self.get(0), deletedContainer);
            });
            //canvas.append(containerEl);
            data.containers.push(container);
            data.containerDetails[container.el[0].id] = containerData;
            data.containerDetails[container.el[0].id].name = containerData.module;
            methods.springyAddContainer.call(this, container);
        },
        //Remove a container
        removeContainer: function(container){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var index = methods.getContainerIndex.call(this, container);
            if(index > -1){
                data.containers.splice(index, 1); //1 indicates to remove only that item
            }
            methods.springyRemoveContainer.call(this, container);
        },
        //Get a container's index
        getContainerIndex: function(container){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            return data.containers.indexOf(container);
        },
        //Add a connection between containers
        addConnection: function(connectionConfig){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var jsplumb_instance = data.jsplumb_instance;
            var defaults = {
				Endpoint : ["Dot", {
					radius:2
				}],
				ConnectorPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				HoverPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				ConnectionOverlays : [
					[ "Arrow", {
						location:1,
						id:"arrow",
						length:10,
						foldback:0.8
					}]
				]
			};
			console.log('containerrrrrr',data.containers);
			var source = data.containers[connectionConfig.sourceId];
			var target = data.containers[connectionConfig.targetId];
			var labelNew  =  connectionConfig.label;
			connectionConfig = {
				sourceId:source.el[0].id,
				taregtId:target.el[0].id,
				source:source.el[0].id,
				target:target.el[0].id,
				label: labelNew,
				Endpoint : ["Dot", {
					radius:2
				}],
				ConnectorPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				HoverPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				ConnectionOverlays : [
					[ "Arrow", {
						location:1,
						id:"arrow",
						length:10,
						foldback:0.8
					}]
				]
				};
            console.log('_addConnectionMethod',connectionConfig);
            connectionConfig = $.extend(true, {Endpoint : ["Dot", {
					radius:2
				}],
				ConnectorPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				HoverPaintStyle : {
					strokeStyle:"#42a62c",
					lineWidth:1
				},
				ConnectionOverlays : [
					[ "Arrow", {
						location:1,
						id:"arrow",
						length:10,
						foldback:0.8
					}]
				]
				}, defaults, connectionConfig || {});
            console.log('connectionConfiggggggg',connectionConfig);
            var newlycreated_conn = jsplumb_instance.connect(connectionConfig, defaults);
            console.log('Newly Created Connection',newlycreated_conn);
            if(connectionConfig.label!='')
            {
                data.connectionDetails[newlycreated_conn.id] = {inputValue:connectionConfig.label};
            }
        },
        //Get the data hash for the instance
        getData: function(){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var result = {
                containers: [],
                connections: []
            }
            console.log('save data',data);
            for(var index = 0; index < data.containers.length; index++){
                var container = data.containers[index];
                var containerData = container.getData();
                console.log('Container '+index+' Data is',containerData);
				var containerDetailsdivId = containerData['config']['divId'];
				containerData['containerDetails'] = [];
				containerData['containerDetails'] = data.containerDetails[containerDetailsdivId];
                result.containers.push(containerData);
            }
            for(var index = 0; index < data.connections.length; index++){
                var connection = data.connections[index];
				var connectionData = connection.getData();
				var connectionDetailsdivId = connectionData['config']['connectionDivId'];
				connectionData['connectionDetails'] = [];
				connectionData['connectionDetails'] = data.connectionDetails[connectionDetailsdivId];
                result.connections.push(connectionData);
            }
            console.log('Populating Result From Ajax ',result);
            return result;
        },
        //Get the data arrays for the instance
        getDataArrays: function(){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var result = {
                containers: data.containers,
                connections: data.connections,
				connectionsDetails: data.connectionDetails,
				containerDetails: data.containerDetails
            }
            return result;
        },
        //Save the wirings
        save: function(self){
            var data = self.data('jsplumb_editor');
            var wiresData = methods.getData.call(this);
            var saveData = (data.options.save || {});
            var url;
            var appId= $("#adapter_id").val();
            data.options.save = saveData;
            if(typeof saveData.url == "function"){
                url = saveData.url.call(this, self);
            }
            else{
                url = saveData.url;
            }
            console.log('Posting Data',wiresData);
            if(url && (url != "")){
                $('.workflow_builder').LoadingOverlay('show',{
                    //background: "rgb(243, 89, 88, 0.8)",
                    //backgroundClass: "bg-info",
                    image : "",
                    //text : "Loading Application Builder..."
                    custom: "<div class=\"progress-circle-indeterminate\" data-color=\"danger\"></div>"
                });
                $.ajax({
                    url: url,
                    data: {
                        wirings: wiresData,
						applicationId: appId,
                    },
                    type: "POST",
                    dataType: "JSON",
                    success: function(response){
                        $('.workflow_builder').LoadingOverlay('hide');
                        //methods.onSaveSuccess.call(self.get(0), response);
                        $('body').pgNotification({
                            style: 'circle',
                            title: '',
                            message: 'Saved Application Successfully',
                            position: 'top-right',
                            timeout: 9000,
                            type: 'success',
                        }).show();
                    },
                    error: function(response){
                        methods.onSaveError.call(self.get(0), response);
                    }
                });
            }
        },
        //Save wirings success
        onSaveSucess: function(response){

        },
        //Save wirings error
        onSaveError: function(response){
            alert(response);
        },
		testFlow: function (self){
        	var applicationId = $('#adapter_id').val();
			modalTestFlow.find('#js-btn-testFlow').unbind('click');
			modalTestFlow.find('#js-btn-testFlow').click(function(e){e.preventDefault();}).click(function(e){
			    e.preventDefault();
			    e.stopPropagation();
                if (modalTestFlow.find('#js-btn-testFlow').hasClass('disabled')) {
                    return false;
                }
			    modalTestFlow.find('#js-btn-testFlow').addClass("disabled");
				var mobileNumber = modalTestFlow.find('#js-test-flow-destination').val();
				$.ajax({
					type: 'POST',
					async:false,
					dataType: 'json',
					url: applicationBaseUrl+'/portal/workflow/app/'+applicationId+'/test',
					data: {
						destination:mobileNumber,
					},
					success: function(response){
                        $('#modalSlideLeftTest .modal-content-wrapper').LoadingOverlay('hide');
                        modalTestFlow.pgNotification({
                            style: 'circle',
                            title: '',
                            message: 'Call initiated successfully',
                            position: 'top-right',
                            timeout: 9000,
                            type: 'success',
                            thumbnail: '<img width="40" height="40" style="display: inline-block;" src="' + assetsBaseUrl + '/img/workflow/dial.svg" data-src="' + assetsBaseUrl + '/img/workflow/dial.svg" data-src-retina="' + assetsBaseUrl + '/img/workflow/dial.svg" alt="">'
                        }).show();
                        modalTestFlow.find('#js-btn-testFlow').removeClass("disabled");
					},
					error: function(response){
						modalTestFlow.find('#js-btn-testFlow').removeClass("disabled");
					}
				});
			});
			modalTestFlow.modal('show');
		},
        //Load the wirings
        load: function(loadParameters){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var loadData = $.extend(true, {}, (data.options.load || {}));
            data.options.load = loadData;
            data.loadParameters = (loadParameters || data.loadParameters || {});
            
            methods.clear.call(this);
            if(typeof loadData.data == "object"){
                methods.onLoadSuccess.call(self.get(0), loadData.data);
            }
            else{
                var url;
                if(typeof loadData.url == "function"){
                    url = loadData.url.call(this, self);
                }
                else{
                    url = loadData.url;
                }
                console.log('loadUrl'+url);
                $.ajax({
                    url: url,
                    data: loadData.parameters,
                    type: "GET",
                    dataType: "JSON",
                    success: function(response){
                        methods.onLoadSuccess.call(self.get(0), response);
                        setTimeout(function(){
                            $('.workflow_builder').LoadingOverlay('hide');
                        }, 5000);
                    },
                    error: function(response){
                        methods.onLoadError.call(self.get(0), response);
                    }
                });
            }
        },
        //Load wirings success
        onLoadSuccess: function(response){
            methods.loadJson.call(this, response);
        },
        //Load wirings error
        onLoadError: function(response){
            alert(response);
        },
        //Reload the editor's data
        reload: function(){
            methods.load.call(this);
        },
        //Clear all the canvas containers
        clear: function(){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var containers = data.containers;
            while (containers.length > 0){
                containers.pop().close();
            }
        },
        //Sort the canvas containers
        sort: function(){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            if(typeof data.springyRenderer == "object"){
                data.springyRenderer.start(function(){
                    });
            }
        },
        
        //Setup springy component variables
        initializeSpringy: function(data){
            var self = $(this);
            data.springyGraph = new Graph();
            data.springyLayout = new Layout.ForceDirected(data.springyGraph, 400.0, 400.0, 0.5);
            data.springyRenderer = new Renderer(100, data.springyLayout,
                function clear() {
                },
                function drawEdge(edge, p1, p2) {
                },
                function drawNode(node, p) {
                    var position = methods.springyToScreen.call(self.get(0), p);
                    var width = $(node.data.container.el).width();
                    var height = $(node.data.container.el).height();
                    node.data.container.setPosition({
                        left: position[0], 
                        top: position[1]
                    }, width, height);
                });
            data.springyRenderer.graphChanged = function(e){};
        },
        //Obtain springy node screen coordinates
        springyToScreen : function(p, containerWidth, containerHeight) {
            var data = $(this).data('jsplumb_editor');
            // calculate bounding box of graph layout.. with ease-in
            var currentBB = data.springyLayout.getBoundingBox();
            var screen = methods.springyScreenSize.call(this);
            var xOffset = (containerWidth / 2) || 0;
            var yOffset = (containerHeight / 2) || 0;
            // convert to/from screen coordinates
            var size = currentBB.topright.subtract(currentBB.bottomleft);
            var sx = (p.subtract(currentBB.bottomleft).divide(size.x).x * screen.x);
            var sy = (p.subtract(currentBB.bottomleft).divide(size.y).y * screen.y);
            return [sx, sy];
        },
        //Get springy node internal coordinates
        springyFromScreen : function(s) {
            var data = $(this).data('jsplumb_editor');
            var screen = methods.springyScreenSize.call(this);
            var currentBB = data.springyLayout.getBoundingBox();
            var size = currentBB.topright.subtract(currentBB.bottomleft);
                    
            var px = (s.x / screen.x) * size.x + currentBB.bottomleft.x;
            var py = (s.y / screen.y) * size.y + currentBB.bottomleft.y;
            return new Vector(px, py);
        },
        //Obtain springy screen size
        springyScreenSize: function() {
            var data = $(this).data('jsplumb_editor');
            var containers = data.containers.length;
            var delta = Math.pow(1.75, Math.round(Math.sqrt(containers)));
            var canvasWidth = delta*75;
            var canvasHeight = delta*50;
            return {
                x: canvasWidth, 
                y: canvasHeight
            }
        },
        //Add springy wire
        springyAddConnection: function(connection){
            var data = $(this).data('jsplumb_editor');
            var source = connection.source.springyNode;
            var target = connection.target.springyNode;
            if(source && target){
                var newData = {
                    length: 7
                };
                connection.springyEdge = data.springyGraph.newEdge(source, target, newData);
            }
        },
        //Remove springy wire
        springyRemoveConnection: function(connection){
            var data = $(this).data('jsplumb_editor');
            if(connection.springyEdge){
                data.springyGraph.removeEdge(connection.springyEdge);
            }
        },
        //Add springy node
        springyAddContainer: function(container){
            var data = $(this).data('jsplumb_editor');
            container.springyNode = data.springyGraph.newNode({
                container: container
            });
            container.initializeSpringyPosition();
        },
        //Remove springy node
        springyRemoveContainer: function(container){
            var data = $(this).data('jsplumb_editor');
            if(container.springyNode)
                data.springyGraph.removeNode(container.springyNode);
        },
        //Initialize the springy component positions
        initializeSpringyPositions: function(){
            var data = $(this).data('jsplumb_editor');
            for(var index = 0; index < this.containers.length; index++){
                data.containers[index].initializeSpringyPosition();
            }
        },
        
        
        
        
        
        
        
        /************************
        * "Private" Methods
        ************************/
        _connectionAdded: function(eventData){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var jsplumb_instance = data.jsplumb_instance;
            console.log('_connectionAdded',eventData);
            var connection = new jsPlumbEditor.Connection(eventData, jsplumb_instance, self, methods);
            data.connections.push(connection);
            methods.springyAddConnection.call(this, connection);
        },
        _connectionRemoved: function(eventData){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            var jspConnection = eventData.connection;
            var connection = jspConnection.editorConnection;
            var index = data.connections.indexOf(connection);
            if(index > -1){
                data.connections.splice(index, 1); //1 indicates to remove only that item
            }
            methods.springyRemoveConnection.call(this, connection);
        },
        _getJsPlumbInstance: function(){
            var self = $(this);
            var data = self.data('jsplumb_editor');
            return data.jsplumb_instance;
        }
    };

    $.fn.jsplumb_editor = function( method ) {
    
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.jsplumb_editor' );
        }    
  
    };

})( jQuery );
//Stop the event's propagation
function stopPropagation(e){
    //IE9 & Other Browsers
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    //IE8 and Lower
    else {
        e.cancelBubble = true;
    }

}