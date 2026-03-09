/**
 * FolkPhotography — Media Grid Hero Toggle
 *
 * Adds a star button (☆ / ★) to each thumbnail in the Media Library grid view.
 * Clicking it toggles the image in/out of the homepage hero rotation without
 * leaving the grid. State is persisted via AJAX to _folk_hero post meta.
 *
 * folkMediaGrid is localised by PHP and contains:
 *   ajaxUrl  — admin-ajax.php URL
 *   nonce    — 'folk_hero_toggle' nonce
 *   heroIds  — array of attachment IDs currently marked as hero
 */
( function ( $ ) {
    'use strict';

    if ( typeof folkMediaGrid === 'undefined' ) return;

    // Local set of hero IDs — kept in sync with server state on each toggle.
    var heroIds = new Set( folkMediaGrid.heroIds.map( Number ) );

    // -------------------------------------------------------------------------
    // Button factory
    // -------------------------------------------------------------------------

    function makeButton( id ) {
        var isHero = heroIds.has( id );
        return $( '<button>', {
            'class':      'folk-hero-toggle' + ( isHero ? ' is-hero' : '' ),
            'data-id':    id,
            'title':      isHero ? 'Remove from hero rotation' : 'Add to hero rotation',
            'aria-label': isHero ? 'Remove from hero rotation' : 'Add to hero rotation',
            'html':       isHero ? '★' : '☆',
        } );
    }

    // -------------------------------------------------------------------------
    // Attach button to a single .attachment grid item (idempotent)
    // -------------------------------------------------------------------------

    function decorateItem( el ) {
        var $el = $( el );
        var id  = parseInt( $el.data( 'id' ), 10 );
        if ( ! id || $el.find( '.folk-hero-toggle' ).length ) return;
        $el.find( '.attachment-preview' ).append( makeButton( id ) );
    }

    // -------------------------------------------------------------------------
    // Scan the whole grid (called on load + after DOM mutations)
    // -------------------------------------------------------------------------

    function decorateAll() {
        $( '.attachments-browser .attachment' ).each( function () {
            decorateItem( this );
        } );
    }

    // -------------------------------------------------------------------------
    // MutationObserver — handles infinite scroll / search result updates
    // -------------------------------------------------------------------------

    function startObserver( container ) {
        var observer = new MutationObserver( function ( mutations ) {
            mutations.forEach( function ( m ) {
                $( m.addedNodes ).each( function () {
                    if ( $( this ).hasClass( 'attachment' ) ) {
                        decorateItem( this );
                    } else {
                        $( this ).find( '.attachment' ).each( function () {
                            decorateItem( this );
                        } );
                    }
                } );
            } );
        } );
        observer.observe( container, { childList: true, subtree: true } );
    }

    // -------------------------------------------------------------------------
    // Boot — wait for the media library Backbone app to render the grid
    // -------------------------------------------------------------------------

    $( function () {
        // The .attachments container may not exist immediately; poll briefly.
        var attempts = 0;
        var interval = setInterval( function () {
            var container = document.querySelector( '.attachments' );
            if ( container ) {
                clearInterval( interval );
                decorateAll();
                startObserver( container );
            }
            if ( ++attempts > 40 ) clearInterval( interval ); // give up after ~4s
        }, 100 );
    } );

    // -------------------------------------------------------------------------
    // Click handler — AJAX toggle
    // -------------------------------------------------------------------------

    $( document ).on( 'click', '.folk-hero-toggle', function ( e ) {
        e.preventDefault();
        e.stopPropagation(); // prevent WP opening the attachment detail panel

        var $btn = $( this );
        var id   = parseInt( $btn.data( 'id' ), 10 );
        if ( ! id || $btn.hasClass( 'is-loading' ) ) return;

        $btn.addClass( 'is-loading' );

        $.post( folkMediaGrid.ajaxUrl, {
            action:        'folk_toggle_hero',
            nonce:         folkMediaGrid.nonce,
            attachment_id: id,
        } )
        .done( function ( response ) {
            if ( ! response.success ) return;
            var isHero = response.data.hero;

            if ( isHero ) {
                heroIds.add( id );
                $btn.addClass( 'is-hero' )
                    .html( '★' )
                    .attr( 'title', 'Remove from hero rotation' )
                    .attr( 'aria-label', 'Remove from hero rotation' );
            } else {
                heroIds.delete( id );
                $btn.removeClass( 'is-hero' )
                    .html( '☆' )
                    .attr( 'title', 'Add to hero rotation' )
                    .attr( 'aria-label', 'Add to hero rotation' );
            }
        } )
        .always( function () {
            $btn.removeClass( 'is-loading' );
        } );
    } );

} )( jQuery );
