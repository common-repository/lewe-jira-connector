/**
 * Lewe Jira Connector Public JS
 *
 * @link       https://www.lewe.com
 * @since      2.0.0
 *
 * @package    Lewe Jira Connector
 * @subpackage Lewe Jira Connector/public/js
 */

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

};

function jcoSortTable(tableid, n) {

  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById(tableid);
  switching = true;
  dir = "asc";

  // Loop until no row switching needed anymore
  while (switching) {

    switching = false;
    rows = table.rows;

    // Loop through all table rows (except the first which contains table headers)
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      // Get the two elements you want to compare, one from current row and one from the next
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      // Check if the two rows should switch place, based on the direction, asc or desc
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }

    if (shouldSwitch) {
      // If a switch has been marked, make the switch and mark that a switch has been done
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase the count by 1
      switchcount++;
    } else {
      // If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }

}

// (function( $ ) {
// 	'use strict';

/**
 * All of the code for your public-facing JavaScript source should reside in this file.
 *
 * Note: It has been assumed you will write jQuery code here, so the $ function reference has been prepared for usage within the scope of this function.
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
 * Ideally, it is not considered best practise to attach more than a single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be practising this, we should strive to set a better example in our own work.
 */

// })( jQuery );

