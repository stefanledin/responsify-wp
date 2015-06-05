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
					name: 'New custom media query',
					mediaQueries: {
						smallestImage: 'thumbnail',
						breakpoints: []
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
						_this.addSetting( new rwp.cmq.models.SettingsModel() );
					});
				},
				initialize: function () {
					this.collection.each(this.addSetting, this);
				},
				addSetting: function (setting) {
					var row = new rwp.cmq.views.SettingsTableRow({
						model: setting
					});
					this.$el.append(row.el);
				}
			}),
			SettingsTableRow: Backbone.View.extend({
				tagName: 'tr',
				events: {
					'click .rwp-add-breakpoint button': 'addMediaQuery'
				},
				addMediaQuery: function (e) {
					e.preventDefault();
					var $breakpointForm = this.$el.find('.rwp-add-breakpoint');
					var mediaQueries = this.model.get('mediaQueries');
					mediaQueries.breakpoints = mediaQueries.breakpoints || [];
					mediaQueries.breakpoints.push({
						imageSize: $breakpointForm.find('.rwp-image-size-select select').val(),
						property: $breakpointForm.find('select[name="property"]').val(),
						value: $breakpointForm.find('input[name="breakpoint"]').val()
					});
					this.model.set('mediaQueries', mediaQueries);
					this.render();
				},
				initialize: function () {
					this.render();
				},
				render: function () {
					this.$el.empty();
					var html = '<td>';
							//html += '<p class="row-title">'+this.model.get('name')+'</p>';
							//html += '<input type="hidden" name="rwp_custom_media_queries['+this.model.cid+'][name]" value="'+this.model.get('name')+'">';
							html += '<p><select>';
								html += '<option>When...</option>';
								html += '<option>Default</option>';
							html += '</select>';
							html += '<select>';
								html += '<option>Post/page ID</option>';
								html += '<option>Page template</option>';
								html += '<option>Image...</option>';
							html += '</select>';
							html += ' is <select>';
								html += '<option>equal to</option>';
								html += '<option>not equal to</option>';
							html += '</select>';
							html += '</p>';
							html += '<div class="rwp-media-query-table"></div>';
						html += '</td>';
						html += '<td>';
							html += '<button class="button">Close</button>';
						html += '</td>';
					this.$el.append(html);
					
					var mediaQueryTable = new rwp.cmq.views.MediaQueryTable({
						//model: this.model.get('mediaQueries')
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
	                            '<th></th>'+
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
								'<td></td>'+
							'</tr>'+
						'</tbody>'
					].join('');	
					this.$el.append(html);
					var select = new rwp.cmq.views.ImageSizeSelect({
						selected: this.model.get('mediaQueries').smallestImage
					});
					select.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][smallestImage]');
					this.$el.find('td.rwp-image-size-select').append(select.el);
				},
				render: function () {
					this.appendHeader();
					this.appendBody();
					var $tbody = this.$el.find('tbody');
					_.each(this.model.get('mediaQueries').breakpoints, function (breakPoint, index) {
						//breakPoint.mediaQueryIndex = index;
						//var row = new rwp.cmq.views.MediaQueryRow(breakPoint);
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
					this.model.get('mediaQueries').breakpoints.splice(arguments.mediaQueryIndex, 1);
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
					this.$el.addClass('edit');
				},
				saveMediaQuery: function (e) {
					e.preventDefault();
					this.$el.removeClass('edit');
					var $selectedSize = this.$el.find('.js-selected-size');
					var $property = this.$el.find('.js-property');
					var $value = this.$el.find('.js-value');

					$selectedSize.find('span').html( $selectedSize.find('select').val() );
					$property.find('span').html( $property.find('select').val() );
					$value.find('span').html( $value.find('input').val() );
				},
				deleteMediaQuery: function (e) {
					e.preventDefault();
					this.remove();
					this.trigger('remove', {
						mediaQueryIndex: this.options.mediaQueryIndex
					});
				},
				initialize: function (options) {
					this.options = options;
					this.render();	
				},
				render: function () {
					var ImageSizeSelect = new rwp.cmq.views.ImageSizeSelect({
						selected: this.options.breakPoint.imageSize
					});
					ImageSizeSelect.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][mediaQueries]['+this.options.mediaQueryIndex+'][image_size]');
					var PropertySelect = new rwp.cmq.views.MediaQueryPropertySelect({
						selected: this.options.breakPoint.property
					});
					PropertySelect.$el.attr('name', 'rwp_custom_media_queries['+this.model.cid+'][mediaQueries]['+this.options.mediaQueryIndex+'][property]');
					var html = [
						'<td class="js-selected-size">'+
							'<span class="hide-on-edit">'+this.options.breakPoint.imageSize+'</span>'+
							'<div class="show-on-edit"></div>'+
						'</td>'+
						'<td class="js-property">'+
							'<span class="hide-on-edit">'+this.options.breakPoint.property+'</span>'+
							'<div class="show-on-edit"></div>'+
						'</td>'+
						'<td class="js-value">'+
							'<span class="hide-on-edit">'+this.options.breakPoint.value+'</span>'+
							'<div class="show-on-edit">'+
								'<input type="text" name="rwp_custom_media_queries['+this.model.cid+'][mediaQueries]['+this.options.mediaQueryIndex+'][value]" value="'+this.options.breakPoint.value+'">'+
							'</div>'+
						'</td>'+
						'<td>'+
							'<div class="hide-on-edit"><button class="button rwp-edit-media-query">Edit</button></div>'+
							'<div class="show-on-edit"><button class="button rwp-save-media-query">Save</button></div>'+
							'<div class="show-on-edit"><button class="button rwp-delete-media-query">Delete</button></div>'+
						'</td>'
					].join('');
					
					this.$el.append(html);
					this.$el.find('td.js-selected-size > .show-on-edit').append(ImageSizeSelect.el);
					this.$el.find('td.js-property > .show-on-edit').append(PropertySelect.el);
					
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
						'<br><label>Add breakpoint '+
							'<div class="rwp-image-size-select" style="display: inline;"></div>'+
							'<select name="property">'+
								'<option>min-width</option>'+
								'<option>max-width</option>'+
							'</select>'+
							'<input type="text" name="breakpoint" placeholder="Breakpoint">'+
							'<button class="button">Add</button>'+
						'</label>'
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
								value: '300px'
							},
							{
								imageSize: 'large',
								property: 'min-width',
								value: '1024px'
							}
						]
					}
				})
			];
			var models = [];
			for (var customMediaQuery in rwp.customMediaQueries) {
				models.push(new rwp.cmq.models.SettingsModel(rwp.customMediaQueries[customMediaQuery]));
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