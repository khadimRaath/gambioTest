'use strict';

/* --------------------------------------------------------------
 initialize_template.js 2016-01-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Initialize Template JS Environment
 *
 * This script will set some parameters needed by other javascript sections. Use it to configure or override code from
 * the JS Engine.
 */

jse.core.config = jse.core.config || {};

jse.libs.template = {}; // Create new libs object for the template libraries.

(function (exports) {

  'use strict';

  // Backup original "init" method.

  var init = jse.core.config.init;

  exports.init = function (jsEngineConfiguration) {
    jse.core.registry.set('mainModalLayer', 'magnific');
    jse.core.registry.set('tplPath', jsEngineConfiguration.tplPath);

    // Call original config file init.
    init(jsEngineConfiguration);
  };
})(jse.core.config);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluaXRpYWxpemVfdGVtcGxhdGUuanMiXSwibmFtZXMiOlsianNlIiwiY29yZSIsImNvbmZpZyIsImxpYnMiLCJ0ZW1wbGF0ZSIsImV4cG9ydHMiLCJpbml0IiwianNFbmdpbmVDb25maWd1cmF0aW9uIiwicmVnaXN0cnkiLCJzZXQiLCJ0cGxQYXRoIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsSUFBSUMsSUFBSixDQUFTQyxNQUFULEdBQWtCRixJQUFJQyxJQUFKLENBQVNDLE1BQVQsSUFBbUIsRUFBckM7O0FBRUFGLElBQUlHLElBQUosQ0FBU0MsUUFBVCxHQUFvQixFQUFwQixDLENBQXdCOztBQUV4QixDQUFDLFVBQVNDLE9BQVQsRUFBa0I7O0FBRWxCOztBQUVBOztBQUNBLE1BQUlDLE9BQU9OLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkksSUFBM0I7O0FBRUFELFVBQVFDLElBQVIsR0FBZSxVQUFTQyxxQkFBVCxFQUFnQztBQUM5Q1AsUUFBSUMsSUFBSixDQUFTTyxRQUFULENBQWtCQyxHQUFsQixDQUFzQixnQkFBdEIsRUFBd0MsVUFBeEM7QUFDQVQsUUFBSUMsSUFBSixDQUFTTyxRQUFULENBQWtCQyxHQUFsQixDQUFzQixTQUF0QixFQUFpQ0Ysc0JBQXNCRyxPQUF2RDs7QUFFQTtBQUNBSixTQUFLQyxxQkFBTDtBQUNBLEdBTkQ7QUFRQSxDQWZELEVBZUdQLElBQUlDLElBQUosQ0FBU0MsTUFmWiIsImZpbGUiOiJpbml0aWFsaXplX3RlbXBsYXRlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbml0aWFsaXplX3RlbXBsYXRlLmpzIDIwMTYtMDEtMjhcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEluaXRpYWxpemUgVGVtcGxhdGUgSlMgRW52aXJvbm1lbnRcbiAqXG4gKiBUaGlzIHNjcmlwdCB3aWxsIHNldCBzb21lIHBhcmFtZXRlcnMgbmVlZGVkIGJ5IG90aGVyIGphdmFzY3JpcHQgc2VjdGlvbnMuIFVzZSBpdCB0byBjb25maWd1cmUgb3Igb3ZlcnJpZGUgY29kZSBmcm9tXG4gKiB0aGUgSlMgRW5naW5lLlxuICovXG5cbmpzZS5jb3JlLmNvbmZpZyA9IGpzZS5jb3JlLmNvbmZpZyB8fCB7fTtcblxuanNlLmxpYnMudGVtcGxhdGUgPSB7fTsgLy8gQ3JlYXRlIG5ldyBsaWJzIG9iamVjdCBmb3IgdGhlIHRlbXBsYXRlIGxpYnJhcmllcy5cblxuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0XG5cdCd1c2Ugc3RyaWN0JztcblxuXHQvLyBCYWNrdXAgb3JpZ2luYWwgXCJpbml0XCIgbWV0aG9kLlxuXHR2YXIgaW5pdCA9IGpzZS5jb3JlLmNvbmZpZy5pbml0O1xuXG5cdGV4cG9ydHMuaW5pdCA9IGZ1bmN0aW9uKGpzRW5naW5lQ29uZmlndXJhdGlvbikge1xuXHRcdGpzZS5jb3JlLnJlZ2lzdHJ5LnNldCgnbWFpbk1vZGFsTGF5ZXInLCAnbWFnbmlmaWMnKTtcblx0XHRqc2UuY29yZS5yZWdpc3RyeS5zZXQoJ3RwbFBhdGgnLCBqc0VuZ2luZUNvbmZpZ3VyYXRpb24udHBsUGF0aCk7XG5cblx0XHQvLyBDYWxsIG9yaWdpbmFsIGNvbmZpZyBmaWxlIGluaXQuXG5cdFx0aW5pdChqc0VuZ2luZUNvbmZpZ3VyYXRpb24pO1xuXHR9O1xuXG59KShqc2UuY29yZS5jb25maWcpO1xuIl19
