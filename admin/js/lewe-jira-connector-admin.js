/**
 * Lewe Jira Connector Admin JS
 *
 * @link       https://www.lewe.com
 * @since      2.0.0
 *
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/admin/js
 */

// (function( $ ) {
// 	'use strict';

/**
 * All of the code for your admin-facing JavaScript source
 * should reside in this file.
 *
 * Note: It has been assumed you will write jQuery code here, so the
 * $ function reference has been prepared for usage within the scope
 * of this function.
 *
 * This enables you to define handlers, for when the DOM is ready:
 *
 * $(function() {
 *
 * });
 *
 * When the window is loaded:
 *
 * $( window ).load(function() {
 *
 * });
 *
 * ...and/or other possibilities.
 *
 * Ideally, it is not considered best practise to attach more than a
 * single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be
 * practising this, we should strive to set a better example in our own work.
 */

// })( jQuery );

/**
 * Hides an HTML parent element via CSS.
 *
 * This function hides the parent element of the given one by setting its
 * display style property to 'none'.
 *
 * @since 1.0.0
 *
 * @param object $el HTML object.
 */
function dismissParent(el) {

  el.parentNode.style.display = 'none';

}
