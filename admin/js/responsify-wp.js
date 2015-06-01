;(function ($) {

	window.rwp = window.rwp || {};

	// Custom Media Queries
	rwp.cmq = {
		collections: {
			SettingsCollection: Backbone.Collection.extend()
		},
		models: {
			SettingsModel: Backbone.Model.extend({
				defaults: {
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
				initialize: function () {
					this.collection.each(function (model) {
						var row = new rwp.cmq.views.SettingsTableRow({
							model: model
						});
						this.$el.append(row.el);
					}, this);
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
							html += '<p class="row-title">'+this.model.get('name')+'</p>';
							html += '<input type="hidden" name="rwp_custom_media_queries[default][name]" value="'+this.model.get('name')+'">';
							html += '<div class="rwp-media-query-table"></div>';
						html += '</td>';
						html += '<td>';
							html += '<button class="button">Close</button>';
						html += '</td>';
					this.$el.append(html);
					
					var mediaQueryTable = new rwp.cmq.views.MediaQueryTable({
						model: this.model.get('mediaQueries')
					});
					var addBreakpoint = new rwp.cmq.views.AddBreakpoint();
					this.$el.find('.rwp-media-query-table').append(mediaQueryTable.el).after(addBreakpoint.el);
					return this;
				}
			}),
			MediaQueryTable: Backbone.View.extend({
				tagName: 'table',
				className: 'wp-list-table widefat',
				initialize: function () {
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
						smallestImage: this.model.smallestImage
					});
					this.$el.find('td.rwp-image-size-select').append(select.el);
				},
				render: function () {
					this.appendHeader();
					this.appendBody();
					var $tbody = this.$el.find('tbody');
					_.each(this.model.breakpoints, function (breakPoint, index) {
						breakPoint.mediaQueryIndex = index;
						var row = new rwp.cmq.views.MediaQueryRow(breakPoint);
						row.on('remove', this.removeRow, this);
						$tbody.append(row.el);
					}, this);
					return this;
				},
				removeRow: function (arguments) {
					this.model.breakpoints.splice(arguments.mediaQueryIndex, 1);
				}
			}),
			MediaQueryRow: Backbone.View.extend({
				tagName: 'tr',
				className: 'sortable',
				events: {
					'click button.rwp-delete-media-query': 'deleteMediaQuery'
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
					var html = [
						'<td>'+this.options.imageSize+'</td>'+
						'<td>'+this.options.property+'</td>'+
						'<td>'+this.options.value+'</td>'+
						'<td><button class="button rwp-edit-media-query">Edit</button>'+
						'	<button class="button rwp-delete-media-query">Delete</button></td>'
					].join('');
					this.$el.append(html);
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
						var selected = (this.options && this.options.smallestImage === size) ? 'selected="selected"' : '';
						var html = '<option value="'+size+'" '+selected+'>'+size+'</option>';
						this.$el.append(html);
					}, this);
					return this;
				}
			})
		},
		init: function () {
			// New up stuff
			var settings = new rwp.cmq.collections.SettingsCollection([
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
				})
			]);
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