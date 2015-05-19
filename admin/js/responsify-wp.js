;(function ($) {

	window.rwp = window.rwp || {};
	
	// Models
	rwp.MediaQueryModel = Backbone.Model.extend();
	rwp.ImageSizeModel = Backbone.Model.extend();
	// Collections
	rwp.MediaQueryCollection = Backbone.Collection.extend({model: rwp.MediaQueryModel});
	rwp.ImageSizesCollection = Backbone.Collection.extend();
	// Views
	rwp.MediaQueryTableView = Backbone.View.extend({
		el: 'tbody.js-media-queries-table',
		add: function (model) {
			var view = new rwp.MediaQueryView({model: model});
			this.$el.append(view.el);
		},
		initialize: function () {
			this.collection.each(this.add, this);
			this.collection.on('add', this.add, this);
		}
	});
	rwp.MediaQueryView = Backbone.View.extend({
		tagName: 'tr',
		className: 'sortable',
		initialize: function () {
			this.render();
		},
		events: {
			'click button.js-delete-media-query': 'deleteMediaQuery'
		},
		deleteMediaQuery: function (e) {
			e.preventDefault();
			this.remove();
			rwp.mediaQueries.remove(this.model);
		},
		render: function () {
			var html = [
				'<td>'+this.model.get('image_size')+'</td>'+
				'<td>'+this.model.get('property')+'</td>'+
				'<td>'+this.model.get('value')+'</td>'+
				'<td><button class="button js-delete-media-query">Delete</button></td>'
			].join('');
			this.$el.append(html);
			return this;
		}
	});
	rwp.AddMediaQueryView = Backbone.View.extend({
		el: '.js-add-new-media-query',
		events: {
			'click button': 'add'
		},
		initialize: function () {
			this.$property = this.$el.find('select[name="property"]');
			this.$value = this.$el.find('input[name="breakpoint"]');
		},
		add: function (e) {
			e.preventDefault();
			var mediaQuery = new rwp.MediaQueryModel({
				image_size: this.$el.find('select[name="image-size"]').val(),
				property: this.$property.val(),
				value: this.$value.val()
			});
			this.$value.val('');
			rwp.mediaQueries.add(mediaQuery);
		}
	});
	// New up stuff
	//rwp.mediaQueries = new rwp.MediaQueryCollection();
	rwp.mediaQueries = new rwp.MediaQueryCollection([
		new rwp.MediaQueryModel({image_size: 'medium', property: 'min-width', value: '150px'}),
		new rwp.MediaQueryModel({image_size: 'large', property: 'min-width', value: '300px'})
	]);
	
	rwp.imageSizes = new rwp.ImageSizesCollection();
	for (var i = rwp.image_sizes.length - 1; i >= 0; i--) {
		rwp.imageSizes.add(new rwp.ImageSizeModel({name: rwp.image_sizes[i]}));
	};
	
	new rwp.AddMediaQueryView;
	new rwp.MediaQueryTableView({collection: rwp.mediaQueries});

	$('.js-media-queries-table').sortable({
		axis: 'y',
		items: '> tr.sortable',
		update: function () {
			
		}
	});

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