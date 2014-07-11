// Scripts file for the admin screens

jQuery(document).ready(function($) {
	pcoImageWidget($); 
});

function pcoImageWidget($) {
	pcoImage = {
		// Load the frame
		frame: function() {
			// Return the frame if it already exists
			if ( this._frame )
				return this._frame;

			// Initialize the frame
			this._frame = wp.media({
				title: data.title,
				frame: 'select', // Post instead will give options for image from url and galleries
				library: { type: 'image' },
				button: { text: data.update },
				multiple: false
			});

			// On specific events
			this._frame.on('open', this.updateFrame);
			this._frame.state('library').on('select', this.select);

			// Save the frame
			return this._frame;
		},

		// When the frame is closing
		select: function() {
			// Closing the frame and selecting image
			var selection = this.get('selection');
			//TODO more images var attachmentIds = [];

			// If a target has been selected get the id and put it inside a variable
			if ( target.length ) {
				target.val( selection.pluck( 'id' ) );
			}

			// For every attachment selected
			selection.map( function( attachment ) {
				// To JSON as we are no longer working with the backbone object
				attachment = attachment.toJSON();

				if ( attachment.id ) {
					// Get the image inside the widget
					var img = $("#pco-image-" + data.target + ".pco-image .image-section div img");
					// Find the right widget from the data target
					var sectionImage = $("#pco-image-" + data.target + " .image-section");
					var sectionNewImage = $("#pco-image-" + data.target + " .newimage-section");
					var imageField = $(sectionImage).find(".pco-image-id");

					//TODO more images attachmentIds.push(attachment.id);
					// Change the src on the image chosen
					// When the image is too small it will not return attachment.sizes
					if( attachment.sizes && attachment.sizes.medium )
						$(img).attr("src", attachment.sizes.medium.url );
					else
						$(img).attr("src", attachment.url );

					// If there is an id show the image and hide the bigger button
					if( imageField.val() > 0 ) {
						sectionImage.show();
						sectionNewImage.hide();
					};
				}
			});

			//TODO more images attachmentIds = attachmentIds.join(",");
		},

		// When the frame is opening
		updateFrame: function() {
			// Get the selected image to also make it selected when we open the frame
			var selection = this.get('library').get('selection');
			var attachment;

			if ( target.length ) {
				// Get the variable from we choose in select and make it selected
				selectedIds = target.val();
				if ( selectedIds && '' !== selectedIds && -1 !== selectedIds && '0' !== selectedIds ) {
					// Get the attachments
					attachment = wp.media.model.Attachment.get( selectedIds );
					attachment.fetch();
				}
			}

			// No idea why we fetch and reset them. But it works
			selection.reset( attachment ? [ attachment ] : [] );
		},

		// Initialize the whole object
		init: function(selectors) {
			// Initialize all pco-image widgets: show or hide image/newImage
			var imageFields = $(".pco-image");
			$.each(imageFields, function() {
				var sectionImage = $(this).find(".image-section");
				var sectionNewImage = $(this).find(".newimage-section");
				var imageField = $(sectionImage).find(".pco-image-id");

				// If there is an id show the image and hide the bigger button
				if( imageField.val() > 0 ) {
					sectionImage.show();
					sectionNewImage.hide();
				};
			});

			// Make sure the markup stays the same even after a click on the save button
			$(selectors).on('click', '.widget-control-save', function() {
				// Do this after the ajax call and the values has been saved
				$(this).ajaxSuccess(function() {
					// Same thing as when we initialized the pco-image widgets. Just only initialize the one with the button clicked
					var form = $(this).closest("form");
					var sectionImage = $(form).find(".image-section");
					var sectionNewImage = $(form).find(".newimage-section");
					var imageField = $(sectionImage).find(".pco-image-id");

					// If there is an id show the image and hide the bigger button
					if( imageField.val() > 0 ) {
						sectionImage.show();
						sectionNewImage.hide();
					}
				});
			});

			// Open media frame when we click the image button
			$(selectors).on('click', '.pco-image-select', function(e) {
				e.preventDefault();

				// Save all the data- attr inside a global variable
				data = $(this).data();
				// The target is the hidden field inside the .image-section
				target = $(".image-section #" + data.target);

				// Open the frame by calling the frame() function
				pcoImage.frame().open();
			});

			// Hide image and set the target field to 0 to also remove it when the widget is saved
			$(selectors).on('click', '.pco-image-remove', function(e) {
				e.preventDefault();

				var data = $(this).data();
				var target = data.target;

				// Set the target value to 0
				$("#pco-image-" + target + " .image-section .pco-image-id").val(0);
				// Hide image section as there now is no image
				$("#pco-image-" + target + " .image-section").hide();
				// Show the newimage section as there now is no image
				$("#pco-image-" + target + " .newimage-section").show();
			});
		}
	};

	var selectors = '#wpbody, #customize-controls';

	pcoImage.init(selectors);
}

