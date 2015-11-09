;(function ($) {

	window.rwp = window.rwp || {};

	// Custom Media Queries
	rwp.cmq = {
		events: {},
		collections: {
			SettingsCollection: Backbone.Collection.extend()
		},
		models: {
			SettingsModel: Backbone.Model.extend({
				defaults: {
					edit_mode: 0,
					name: '',
					rule: {
						default: 'true',
						when: {}
					}
				}
			}),
		},
		views: {
			SettingsTable: Backbone.View.extend({
				el: 'tbody.rwp-custom-media-queries',
				events: function () {
					var _this = this;

					$('.rwp-add-setting').find('button').on('click', function (e) {
						e.preventDefault();
						$(this).blur();
						_this.addSetting( new rwp.cmq.models.SettingsModel({
							edit_mode: 1
						}) );
					});
				},
				initialize: function () {
					this.collection.each(this.addSetting, this);
					this.$el.sortable({
						axis: 'y',
						items: '> tr.sortable'
					});
				},
				addSetting: function (setting) {
					var row = new rwp.cmq.views.SettingsTableRow({
						model: setting
					});
					this.$el.append(row.el);
					row.$el.find('input.rwp-setting-name').focus();
				}
			}),
			SettingsTableRow: Backbone.View.extend({
				tagName: 'tr',
				attributes: {
					'class': 'sortable'
				},
				events: {
					'click .rwp-add-breakpoint button': 'addMediaQuery',
					'keydown input[name="breakpoint"]': 'addMediaQueryOnReturn',
					'click .row-title a': 'toggleSettings',
					'click .edit a': 'toggleSettings',
					'click a.submitdelete': 'deleteMediaQuery',
					'blur input.rwp-setting-name': 'updateSettingName'
				},
				updateSettingName: function (e) {
					this.model.set('name', e.currentTarget.value);
				},
				toggleSettings: function (e) {
					e.preventDefault();
					$(e.currentTarget).blur();
					if (this.model.get('edit_mode')) {
						this.hideSettings();
					} else {
						this.showSettings();
					}
				},
				showSettings: function () {
					this.model.set('edit_mode', 1);
					this.$el.addClass('rwp-setting-row-open');
				},
				hideSettings: function () {
					this.model.set('edit_mode', 0);
					this.$el.removeClass('rwp-setting-row-open');
				},
				addMediaQueryOnReturn: function (e) {
					if (e.keyCode === 13) {
						e.preventDefault();
						this.addMediaQuery(e);
					}
				},
				addMediaQuery: function (e) {
					e.preventDefault();
					var $breakpointForm = this.$el.find('.rwp-add-breakpoint');
					if (!this.model.get('breakpoints')) {
						this.model.set('breakpoints', []);
					}
					this.model.get('breakpoints').push({
						image_size: $breakpointForm.find('.rwp-image-size-select select').val(),
						property: $breakpointForm.find('select[name="property"]').val(),
						value: $breakpointForm.find('input[name="breakpoint"]').val()
					});
					this.render();
				},
				deleteMediaQuery: function (e) {
					e.preventDefault();
					if (window.confirm('Are you sure?')) {
						this.remove();
					}
				},
				initialize: function () {
					this.model.on('change:name', this.render, this);
					this.render();
				},
				render: function () {
					this.$el.empty();
					if (this.model.get('edit_mode')) {
						this.$el.addClass('rwp-setting-row-open');
					}
					var html = '<td>';
							html += '<div class="row-title">';
								var name = (this.model.get('name') === '') ? 'New custom media query' : this.model.get('name');
								html += '<a class="rwp-hide-when-editing-setting" href="#">'+name+'</a>';
								html += '<div class="rwp-show-when-editing-setting">';
									html += '<input class="rwp-setting-name" type="text" name="rwp_custom_media_queries['+this.model.cid+'][name]" size="30" placeholder="New custom media query" value="'+this.model.get('name')+'">';
								html += '</div>';
							html += '</div>';
							html += '<div class="row-actions">';
								html += '<span class="edit"><a href="#"><span class="rwp-hide-when-editing-setting">Edit</span><span class="rwp-show-when-editing-setting">Close</span></a></span> | ';
								html += '<span class="trash"><a class="submitdelete" href="#">Delete</a></span>';
							html += '</div>';
							html += '<div class="rwp-setting-wrapper">';
								html += '<div class="rwp-setting-rules"></div>';
								html += '<div class="rwp-media-query-table"></div>';
							html += '</div>';
						html += '</td>';
					this.$el.append(html);
					
					var settingRules = new rwp.cmq.views.SettingRules({
						model: this.model
					});
					this.$el.find('.rwp-setting-rules').append(settingRules.el);
					var mediaQueryTable = new rwp.cmq.views.MediaQueryTable({
						model: this.model
					});
					var addBreakpoint = new rwp.cmq.views.AddBreakpoint();
					this.$el.find('.rwp-media-query-table').append(mediaQueryTable.el).after(addBreakpoint.el);
					return this;
				}
			}),
			MediaQueryTable: Backbone.View.extend({
				tagName: 'table',
				className: 'wp-list-table widefat',
				events: {
					'change select': 'saveSmallestImageSize'
				},
				saveSmallestImageSize: function (e) {
					this.model.set('smallestImage', e.currentTarget.value);	
				},
				initialize: function (options) {
					this.options = options;
					this.render();
					this.$el.find('tbody.rwp-media-queries-table').sortable({
						axis: 'y',
						items: '> tr.sortable'
					});
				},
				appendHeader: function () {
					var html = [
						'<thead>'+
	                        '<tr>'+
	                            '<th>Image size</th>'+
	                            '<th>Property</th>'+
	                            '<th>Value</th>'+
	                            '<th>&nbsp;</th>'+
	                        '</tr>'+
	                    '</thead>'
					].join('');
					this.$el.append(html);
				},
				appendBody: function () {
					var html = [
						'<tbody class="rwp-media-queries-table">'+
							'<tr>'+
								'<td class="rwp-image-size-select"><p>Smallest image size:</p>'+
								'</td>'+
								'<td></td>'+
								'<td></td>'+
							'</tr>'+
						'</tbody>'
					].join('');	
					this.$el.append(html);
					var select = new rwp.cmq.views.ImageSizeSelect({
						selected: this.model.get('smallestImage')
					});
					select.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][smallestImage]');
					this.$el.find('td.rwp-image-size-select').append(select.el);
				},
				render: function () {
					this.appendHeader();
					this.appendBody();
					var $tbody = this.$el.find('tbody');
					_.each(this.model.get('breakpoints'), function (breakPoint, index) {
						var row = new rwp.cmq.views.MediaQueryRow({
							model: this.model,
							breakPoint: breakPoint,
							mediaQueryIndex: index
						});
						row.on('remove', this.removeRow, this);
						$tbody.append(row.el);
					}, this);
					return this;
				},
				removeRow: function (arguments) {
					this.model.get('breakpoints').splice(arguments.mediaQueryIndex, 1);
				}
			}),
			MediaQueryRow: Backbone.View.extend({
				tagName: 'tr',
				className: 'sortable',
				events: {
					'click button.rwp-delete-media-query': 'deleteMediaQuery',
					'click button.rwp-edit-media-query': 'editMediaQuery',
					'click button.rwp-save-media-query': 'saveMediaQuery'
				},
				editMediaQuery: function (e) {
					e.preventDefault();
					this.$el.addClass('rwp-editing-breakpoint');
				},
				saveMediaQuery: function (e) {
					e.preventDefault();
					this.$el.removeClass('rwp-editing-breakpoint');
					var $selectedSize = this.$el.find('.js-selected-size');
					var $property = this.$el.find('.js-property');
					var $value = this.$el.find('.js-value');

					$selectedSize.find('span').html( $selectedSize.find('select').val() );
					$property.find('span').html( $property.find('select').val() );
					$value.find('span').html( $value.find('input').val() );
				},
				deleteMediaQuery: function (e) {
					e.preventDefault();
					if (window.confirm('Are you sure?')) {
						this.remove();
						this.trigger('remove', {
							mediaQueryIndex: this.options.mediaQueryIndex
						});
					}
				},
				initialize: function (options) {
					this.options = options;
					this.render();	
				},
				render: function () {
					var ImageSizeSelect = new rwp.cmq.views.ImageSizeSelect({
						selected: this.options.breakPoint.image_size
					});
					ImageSizeSelect.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][breakpoints]['+this.options.mediaQueryIndex+'][image_size]');
					var PropertySelect = new rwp.cmq.views.MediaQueryPropertySelect({
						selected: this.options.breakPoint.property
					});
					PropertySelect.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][breakpoints]['+this.options.mediaQueryIndex+'][property]');
					var html = [
						'<td class="js-selected-size">'+
							'<span class="rwp-hide-when-editing-breakpoints">'+this.options.breakPoint.image_size+'</span>'+
							'<div class="rwp-show-when-editing-breakpoints"></div>'+
						'</td>'+
						'<td class="js-property">'+
							'<span class="rwp-hide-when-editing-breakpoints">'+this.options.breakPoint.property+'</span>'+
							'<div class="rwp-show-when-editing-breakpoints"></div>'+
						'</td>'+
						'<td class="js-value">'+
							'<span class="rwp-hide-when-editing-breakpoints">'+this.options.breakPoint.value+'</span>'+
							'<div class="rwp-show-when-editing-breakpoints">'+
								'<input type="text" name="rwp_custom_media_queries['+this.model.cid+'][breakpoints]['+this.options.mediaQueryIndex+'][value]" value="'+this.options.breakPoint.value+'">'+
							'</div>'+
						'</td>'+
						'<td>'+
							'<div class="rwp-hide-when-editing-breakpoints"><button class="button rwp-edit-media-query">Edit</button></div>'+
							'<div class="rwp-show-when-editing-breakpoints"><button class="button rwp-save-media-query">Save</button></div>'+
							'<div class="rwp-show-when-editing-breakpoints"><button class="button rwp-delete-media-query">Delete</button></div>'+
						'</td>'
					].join('');
					
					this.$el.append(html);
					this.$el.find('td.js-selected-size > .rwp-show-when-editing-breakpoints').append(ImageSizeSelect.el);
					this.$el.find('td.js-property > .rwp-show-when-editing-breakpoints').append(PropertySelect.el);
					
					return this;
				}
			}),
			SettingRules: Backbone.View.extend({
				elements: {},
				events: {
					'change select.rwp-setting-rule-default': 'updateRules',
					'change select.rwp-setting-rule-when': 'updateRules',
					'blur input.rwp-setting-rule-value': 'updateRules'
				},
				updateRules: function (e) {
					var rule = this.model.get('rule');
					rule.default = this.$el.find('select.rwp-setting-rule-default').val();
					rule.when.key = this.$el.find('select.rwp-setting-rule-when').val();
					rule.when.value = this.$el.find('input.rwp-setting-rule-value').val();
					this.model.set(rule);
					this.model.trigger('change:rule');
				},
				initialize: function () {
					this.model.on('change:rule', this.scenarioBuilderVisibility, this);

					this.render();
					
					this.elements.$scenarioBuilder = this.$el.find('.rwp-setting-rule-scenario-builder');
					this.$el.find('select.rwp-setting-rule-default')[0].value = this.model.get('rule').default;
					this.$el.find('select.rwp-setting-rule-when')[0].value = this.model.get('rule').when.key;
					this.$el.find('select.rwp-setting-rule-when-image')[0].value = this.model.get('rule').when.image;
					this.$el.find('select.rwp-setting-rule-compare')[0].value = this.model.get('rule').when.compare;
					if (this.model.get('rule').when.value) {
						this.$el.find('input.rwp-setting-rule-value')[0].value = this.model.get('rule').when.value;
					}
					this.scenarioBuilderVisibility();
				},
				scenarioBuilderVisibility: function () {
					var scenarioBuilderdisplayValue = (this.model.get('rule').default === 'true') ? 'none' : 'inline';
					var whenImageDisplayValue = (this.model.get('rule').when.key === 'image') ? 'inline' : 'none';
					this.$el.find('.rwp-setting-rule-scenario-builder').css('display', scenarioBuilderdisplayValue);
					this.$el.find('.rwp-setting-rule-when-image').css('display', whenImageDisplayValue);
				},
				render: function () {
					var name = 'rwp_custom_media_queries['+this.model.cid+'][rule]';
					var html = '';
					
					html += '<select class="rwp-setting-rule-default" name="'+name+'[default]">';
						html += '<option value="true">Use as default setting</option>';
						html += '<option value="false">Use when...</option>';
					html += '</select>';
					
					html += '<div class="rwp-setting-rule-scenario-builder">';
						html += '<select class="rwp-setting-rule-when" name="'+name+'[when][key]">';
							html += '<option value="page_id">Page ID</option>';
							html += '<option value="page_slug">Page slug</option>';
							html += '<option value="page_template">Page template</option>';
							html += '<option value="image">Image</option>';
						html += '</select>';
						html += '<select class="rwp-setting-rule-when-image" name="'+name+'[when][image]">';
							html += '<option value="class">class</option>';
							html += '<option value="size">size is</option>';
						html += '</select>';
						html += ' <select class="rwp-setting-rule-compare" name="'+name+'[when][compare]">';
							html += '<option value="equals">is equal to</option>';
							html += '<option value="not_equals">is not equal to</option>';
						html += '</select>';
						html += '<input type="text" class="rwp-setting-rule-value" name="'+name+'[when][value]">';
					html += '</div>';
					
					this.$el.html(html);
					return this;
				}
			}),
			AddBreakpoint: Backbone.View.extend({
				className: 'rwp-add-breakpoint',
				initialize: function () {
					this.render();
				},
				render: function () {
					var select = new rwp.cmq.views.ImageSizeSelect();
					var html = [
						'<br><label>Add breakpoint: </label>'+
							'<div class="rwp-image-size-select" style="display: inline;"></div>'+
							'<select name="property">'+
								'<option>min-width</option>'+
								'<option>max-width</option>'+
							'</select>'+
							'<input type="text" name="breakpoint" placeholder="Breakpoint">'+
							'<button class="button">Add</button>'
					].join('');
					this.$el.append(html);
					this.$el.find('div.rwp-image-size-select').append(select.el);
					return this;
				}
			}),
			ImageSizeSelect: Backbone.View.extend({
				tagName: 'select',
				initialize: function (options) {
					this.options = options;
					this.render();
				},
				render: function () {
					_.each(rwp.image_sizes, function (size) {
						var selected = (this.options && this.options.selected === size) ? 'selected="selected"' : '';
						var html = '<option value="'+size+'" '+selected+'>'+size+'</option>';
						this.$el.append(html);
					}, this);
					return this;
				}
			}),
			MediaQueryPropertySelect: Backbone.View.extend({
				tagName: 'select',
				initialize: function (options) {
					this.options = options;
					this.render();
				},
				render: function () {
					var options = ['min-width', 'max-width'];
					var html = '';
					for (var i = 0; i < options.length; i++) {
						var selectedAttribute = (this.options && (this.options.selected == options[i])) ? 'selected="selected"' : '';
						html += '<option value="'+options[i]+'" '+selectedAttribute+'>'+options[i]+'</option>';
					}
					this.$el.append(html);
					return this;
				}
			})
		},
		init: function () {
			// New up stuff
			var placeholder = [
				new rwp.cmq.models.SettingsModel({
					name: 'Default',
					mediaQueries: {
						smallestImage: 'thumbnail',
						breakpoints: [
							{
								imageSize: 'medium',
								property: 'min-width',
								value: '300px'
							},
							{
								imageSize: 'large',
								property: 'min-width',
								value: '1024px'
							}
						]
					}
				}),
				new rwp.cmq.models.SettingsModel({
					name: 'when image class is equal to wp-size-medium',
					mediaQueries: {
						smallestImage: 'thumbnail',
						breakpoints: [
							{
								imageSize: 'medium',
								property: 'min-width',
								value: '500px'
							},
							{
								imageSize: 'large',
								property: 'min-width',
								value: '1440px'
							}
						]
					}
				})
			];
			var models = [];
			if(rwp.customMediaQueries) {
				for (var customMediaQuery in rwp.customMediaQueries) {
					models.push(new rwp.cmq.models.SettingsModel(rwp.customMediaQueries[customMediaQuery]));
				}
			}
			var settings = new rwp.cmq.collections.SettingsCollection(models);
			var settingsTable = new rwp.cmq.views.SettingsTable({
				collection: settings
			});
		}
	};
	rwp.cmq.init();

	

	$('div.input-group').find('input').on('click', function () {
		var id, displayValue, $root;
		$root = $(this).parents('div.input-group');
		if ($(this).hasClass('js-has-message')) {
			id = $(this).attr('data-message');
			displayValue = 'block';
		} else {
			id = $root.find('input.js-has-message').attr('data-message');
			displayValue = 'none';
		}
		$('.option-message#'+id).css('display', displayValue);
	});

})(jQuery);
