( function( window, document ) {
  function film_maker_lite_keepFocusInMenu() {
    document.addEventListener( 'keydown', function( e ) {
      const film_maker_lite_nav = document.querySelector( '.sidenav' );
      if ( ! film_maker_lite_nav || ! film_maker_lite_nav.classList.contains( 'open' ) ) {
        return;
      }
      const elements = [...film_maker_lite_nav.querySelectorAll( 'input, a, button' )],
        film_maker_lite_lastEl = elements[ elements.length - 1 ],
        film_maker_lite_firstEl = elements[0],
        film_maker_lite_activeEl = document.activeElement,
        tabKey = e.keyCode === 9,
        shiftKey = e.shiftKey;
      if ( ! shiftKey && tabKey && film_maker_lite_lastEl === film_maker_lite_activeEl ) {
        e.preventDefault();
        film_maker_lite_firstEl.focus();
      }
      if ( shiftKey && tabKey && film_maker_lite_firstEl === film_maker_lite_activeEl ) {
        e.preventDefault();
        film_maker_lite_lastEl.focus();
      }
    } );
  }
  film_maker_lite_keepFocusInMenu();
} )( window, document );