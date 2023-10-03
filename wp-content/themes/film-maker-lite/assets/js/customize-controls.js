( function( api ) {

	// Extends our custom "film-maker-lite" section.
	api.sectionConstructor['film-maker-lite'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );