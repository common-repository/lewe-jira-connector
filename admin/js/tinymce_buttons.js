/**
 * Bootstrap Visuals TinyMCE Buttons
 *
 * Custom Javascript functions for the Bootstrap Visuals plugin.
 *
 * @author George Lewe
 * @link https://www.lewe.com
 *
 * @package Bootstrap Visuals
 * @subpackage Scripts
 * @since 1.2.0
 */
(function () {

  tinymce.PluginManager.add('jcobtn', function (editor, url) {
    //
    // Submenu Array
    //
    var menu_array = [];

    //
    // Add the menu button
    //
    editor.addButton('jcobtn', {

      title: 'Lewe Jira Connector',
      type: 'menubutton',
      image: url + '/icon-20x20.png',
      menu: menu_array

    });

    //
    // Menu entry
    //
    menu_array.push({

      text: "Jira Issue (Button)",
      value: '[jco-issue host="" format="button" key=""]',

      onclick: function () {

        editor.insertContent(this.value());

      }

    });

    //
    // Menu entry
    //
    menu_array.push({

      text: "Jira Issue (Inline)",
      value: '[jco-issue host="" format="inline" key=""]',

      onclick: function () {

        editor.insertContent(this.value());

      }

    });

    //
    // Menu entry
    //
    menu_array.push({

      text: "Jira Issue (Panel)",
      value: '[jco-issue host="" format="panel" key=""]',

      onclick: function () {

        editor.insertContent(this.value());

      }

    });

    //
    // Menu entry
    //
    menu_array.push({

      text: "Jira Filter",
      value: '[jco-filter host="" jql="" fields="issuetype,priority,key,summary,status"]',

      onclick: function () {

        editor.insertContent(this.value());

      }

    });

    //
    // Separator
    //
    menu_array.push({

      text: "---",
      value: '',
      onclick: function () {
      }

    });

    //
    // Menu entry
    //
    menu_array.push({

      text: "Documentation...",
      value: '',
      onclick: function () {
        window.open('https://lewe.gitbook.io/lewe-jira-connector-for-wordpress/', '_blank');
      }

    });

  });

})();
