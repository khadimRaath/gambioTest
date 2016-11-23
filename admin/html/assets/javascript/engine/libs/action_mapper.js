'use strict';

/* --------------------------------------------------------------
 action_mapper.js 2016-02-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.action_mapper = jse.libs.action_mapper || {};

/**
 * ## Action Mapper Library
 *
 * Maps a dropdown button action item event to another page element ($button). This library
 * must be used to quickly redirect user actions to existing but hidden UI elements like table row
 * actions. When a callback function is passed as an argument the action item will override the default
 * behaviour.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * 
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *   
 *   [
 *      gx.source + '/libs/action_mapper'   
 *   ],
 *   
 *   function(data) {
 *      // Module code ... 
 *   }); 
 *```
 * ### Example
 *
 * The HTML for the target button: 
 * 
 * ```html
 * <button id="button1">Button 1</button>
 * ```
 *
 * The JavaScript code that will map an action to to a button dropdown widget for the target button:
 * 
 * ```javascript
 * // Define a custom callback function.
 * function customCallbackFunc(event) {
 *     console.log('Function called!');
 * };
 *
 * // Map an event to a new dropdown action item.
 * var options = {
 *   // A new action item will be created in this widget.
 *   $dropdown: $('#button-dropdown'), 
 *
 *   // Target element will be triggered when the user clicks the dropdown action item.  
 *   $target: $('#target-button'), 
 *   
 *   // Target event name to be triggered.
 *   event: 'click',   
 *   
 *   // (optional) Provide a function to override the default event handler.
 *   callback: customCallbackFunc, 
 *   
 *   // (optional) Add a custom action title for the dropdown button.
 *   title: 'Action Title' 
 * }
 * 
 * jse.libs.action_mapper.bind(options);
 * ```
 *
 * By clicking on the "Button 1" you will receive a "Function called!" in the console!
 *
 * @module Admin/Libs/action_mapper
 * @exports jse.libs.action_mapper
 */
(function (exports) {

  'use strict';

  /**
   * Triggers a specific event from an element.
   *
   * Some situations require a different approach than just using the "trigger" method.
   *
   * @param {object} $element Destination element to be triggered.
   * @param {object} event Event options can be used for creating new conditions.
   *
   * @private
   */

  var _triggerEvent = function _triggerEvent($element, event) {
    if ($element.prop('tagName') === 'A' && event.type === 'click') {
      $element.get(0).click();
    } else {
      $element.trigger(event.type);
    }
  };

  /**
   * Binds the event to a new dropdown action item.
   *
   * @param options See bind documentation.
   *
   * @private
   */
  var _bindEvent = function _bindEvent(options) {
    var $dropdown = options.$dropdown,
        action = options.action,
        $target = options.$target,
        eventName = options.event,
        callback = options.callback || false,
        title = options.title || (options.$target.length ? options.$target.text() : '<No Action Title Provided>'),
        $li = $('<li></li>');

    $li.html('<span data-value="' + action + '">' + title + '</span>');
    $dropdown.find('ul').append($li);

    $li.find('span').on(eventName, function (event) {
      if (callback !== false) {
        //event.preventDefault();
        //event.stopPropagation();
        callback.call($li.find('span'), event);
      } else {
        _triggerEvent($target, event);
      }
    });
  };

  /**
   * Binds the event
   *
   * This method is the initializing point for all event bindings.
   *
   * @param {object} options Contains all elements, function and event description
   * @param {string} options.$dropdown Selector for the button dropdown element (div).
   * @param {string} [options.$target] (optional) Selector for the target element of the mapping.
   * @param {string} options.event The name of the event. The event will be triggered on source and
   * destination element (e.g. "click", "mouseleave").
   * @param {function} [options.callback] (optional) Function that will be called when the event of the
   * destination element is triggered. OVERWRITES THE ACTUAL EVENT FOR THE  DESTINATION ELEMENT.
   * @param {string} title (optional) Provide an action title for the dropdown if no $target was defined.
   */
  exports.bind = function (options) {
    _bindEvent(options);
  };
})(jse.libs.action_mapper);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFjdGlvbl9tYXBwZXIuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImFjdGlvbl9tYXBwZXIiLCJleHBvcnRzIiwiX3RyaWdnZXJFdmVudCIsIiRlbGVtZW50IiwiZXZlbnQiLCJwcm9wIiwidHlwZSIsImdldCIsImNsaWNrIiwidHJpZ2dlciIsIl9iaW5kRXZlbnQiLCJvcHRpb25zIiwiJGRyb3Bkb3duIiwiYWN0aW9uIiwiJHRhcmdldCIsImV2ZW50TmFtZSIsImNhbGxiYWNrIiwidGl0bGUiLCJsZW5ndGgiLCJ0ZXh0IiwiJGxpIiwiJCIsImh0bWwiLCJmaW5kIiwiYXBwZW5kIiwib24iLCJjYWxsIiwiYmluZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxJQUFJQyxJQUFKLENBQVNDLGFBQVQsR0FBeUJGLElBQUlDLElBQUosQ0FBU0MsYUFBVCxJQUEwQixFQUFuRDs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQWdFQSxDQUFDLFVBQVNDLE9BQVQsRUFBa0I7O0FBRWxCOztBQUVBOzs7Ozs7Ozs7OztBQVVBLE1BQUlDLGdCQUFnQixTQUFoQkEsYUFBZ0IsQ0FBU0MsUUFBVCxFQUFtQkMsS0FBbkIsRUFBMEI7QUFDN0MsUUFBSUQsU0FBU0UsSUFBVCxDQUFjLFNBQWQsTUFBNkIsR0FBN0IsSUFBb0NELE1BQU1FLElBQU4sS0FBZSxPQUF2RCxFQUFnRTtBQUMvREgsZUFBU0ksR0FBVCxDQUFhLENBQWIsRUFBZ0JDLEtBQWhCO0FBQ0EsS0FGRCxNQUVPO0FBQ05MLGVBQVNNLE9BQVQsQ0FBaUJMLE1BQU1FLElBQXZCO0FBQ0E7QUFDRCxHQU5EOztBQVFBOzs7Ozs7O0FBT0EsTUFBSUksYUFBYSxTQUFiQSxVQUFhLENBQVNDLE9BQVQsRUFBa0I7QUFDbEMsUUFBSUMsWUFBWUQsUUFBUUMsU0FBeEI7QUFBQSxRQUNDQyxTQUFTRixRQUFRRSxNQURsQjtBQUFBLFFBRUNDLFVBQVVILFFBQVFHLE9BRm5CO0FBQUEsUUFHQ0MsWUFBWUosUUFBUVAsS0FIckI7QUFBQSxRQUlDWSxXQUFXTCxRQUFRSyxRQUFSLElBQW9CLEtBSmhDO0FBQUEsUUFLQ0MsUUFBUU4sUUFBUU0sS0FBUixLQUFrQk4sUUFBUUcsT0FBUixDQUFnQkksTUFBaEIsR0FBeUJQLFFBQVFHLE9BQVIsQ0FBZ0JLLElBQWhCLEVBQXpCLEdBQWtELDRCQUFwRSxDQUxUO0FBQUEsUUFNQ0MsTUFBTUMsRUFBRSxXQUFGLENBTlA7O0FBUUFELFFBQUlFLElBQUosQ0FBUyx1QkFBdUJULE1BQXZCLEdBQWdDLElBQWhDLEdBQXVDSSxLQUF2QyxHQUErQyxTQUF4RDtBQUNBTCxjQUFVVyxJQUFWLENBQWUsSUFBZixFQUFxQkMsTUFBckIsQ0FBNEJKLEdBQTVCOztBQUVBQSxRQUFJRyxJQUFKLENBQVMsTUFBVCxFQUFpQkUsRUFBakIsQ0FBb0JWLFNBQXBCLEVBQStCLFVBQVNYLEtBQVQsRUFBZ0I7QUFDOUMsVUFBSVksYUFBYSxLQUFqQixFQUF3QjtBQUN2QjtBQUNBO0FBQ0FBLGlCQUFTVSxJQUFULENBQWNOLElBQUlHLElBQUosQ0FBUyxNQUFULENBQWQsRUFBZ0NuQixLQUFoQztBQUNBLE9BSkQsTUFJTztBQUNORixzQkFBY1ksT0FBZCxFQUF1QlYsS0FBdkI7QUFDQTtBQUNELEtBUkQ7QUFTQSxHQXJCRDs7QUF1QkE7Ozs7Ozs7Ozs7Ozs7O0FBY0FILFVBQVEwQixJQUFSLEdBQWUsVUFBU2hCLE9BQVQsRUFBa0I7QUFDaENELGVBQVdDLE9BQVg7QUFDQSxHQUZEO0FBSUEsQ0F0RUQsRUFzRUdiLElBQUlDLElBQUosQ0FBU0MsYUF0RVoiLCJmaWxlIjoiYWN0aW9uX21hcHBlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYWN0aW9uX21hcHBlci5qcyAyMDE2LTAyLTIyXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMuYWN0aW9uX21hcHBlciA9IGpzZS5saWJzLmFjdGlvbl9tYXBwZXIgfHwge307XG5cbi8qKlxuICogIyMgQWN0aW9uIE1hcHBlciBMaWJyYXJ5XG4gKlxuICogTWFwcyBhIGRyb3Bkb3duIGJ1dHRvbiBhY3Rpb24gaXRlbSBldmVudCB0byBhbm90aGVyIHBhZ2UgZWxlbWVudCAoJGJ1dHRvbikuIFRoaXMgbGlicmFyeVxuICogbXVzdCBiZSB1c2VkIHRvIHF1aWNrbHkgcmVkaXJlY3QgdXNlciBhY3Rpb25zIHRvIGV4aXN0aW5nIGJ1dCBoaWRkZW4gVUkgZWxlbWVudHMgbGlrZSB0YWJsZSByb3dcbiAqIGFjdGlvbnMuIFdoZW4gYSBjYWxsYmFjayBmdW5jdGlvbiBpcyBwYXNzZWQgYXMgYW4gYXJndW1lbnQgdGhlIGFjdGlvbiBpdGVtIHdpbGwgb3ZlcnJpZGUgdGhlIGRlZmF1bHRcbiAqIGJlaGF2aW91ci5cbiAqXG4gKiBZb3Ugd2lsbCBuZWVkIHRvIHByb3ZpZGUgdGhlIGZ1bGwgVVJMIGluIG9yZGVyIHRvIGxvYWQgdGhpcyBsaWJyYXJ5IGFzIGEgZGVwZW5kZW5jeSB0byBhIG1vZHVsZTpcbiAqIFxuICogYGBgamF2YXNjcmlwdFxuICogZ3guY29udHJvbGxlci5tb2R1bGUoXG4gKiAgICdteV9jdXN0b21fcGFnZScsXG4gKiAgIFxuICogICBbXG4gKiAgICAgIGd4LnNvdXJjZSArICcvbGlicy9hY3Rpb25fbWFwcGVyJyAgIFxuICogICBdLFxuICogICBcbiAqICAgZnVuY3Rpb24oZGF0YSkge1xuICogICAgICAvLyBNb2R1bGUgY29kZSAuLi4gXG4gKiAgIH0pOyBcbiAqYGBgXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIFRoZSBIVE1MIGZvciB0aGUgdGFyZ2V0IGJ1dHRvbjogXG4gKiBcbiAqIGBgYGh0bWxcbiAqIDxidXR0b24gaWQ9XCJidXR0b24xXCI+QnV0dG9uIDE8L2J1dHRvbj5cbiAqIGBgYFxuICpcbiAqIFRoZSBKYXZhU2NyaXB0IGNvZGUgdGhhdCB3aWxsIG1hcCBhbiBhY3Rpb24gdG8gdG8gYSBidXR0b24gZHJvcGRvd24gd2lkZ2V0IGZvciB0aGUgdGFyZ2V0IGJ1dHRvbjpcbiAqIFxuICogYGBgamF2YXNjcmlwdFxuICogLy8gRGVmaW5lIGEgY3VzdG9tIGNhbGxiYWNrIGZ1bmN0aW9uLlxuICogZnVuY3Rpb24gY3VzdG9tQ2FsbGJhY2tGdW5jKGV2ZW50KSB7XG4gKiAgICAgY29uc29sZS5sb2coJ0Z1bmN0aW9uIGNhbGxlZCEnKTtcbiAqIH07XG4gKlxuICogLy8gTWFwIGFuIGV2ZW50IHRvIGEgbmV3IGRyb3Bkb3duIGFjdGlvbiBpdGVtLlxuICogdmFyIG9wdGlvbnMgPSB7XG4gKiAgIC8vIEEgbmV3IGFjdGlvbiBpdGVtIHdpbGwgYmUgY3JlYXRlZCBpbiB0aGlzIHdpZGdldC5cbiAqICAgJGRyb3Bkb3duOiAkKCcjYnV0dG9uLWRyb3Bkb3duJyksIFxuICpcbiAqICAgLy8gVGFyZ2V0IGVsZW1lbnQgd2lsbCBiZSB0cmlnZ2VyZWQgd2hlbiB0aGUgdXNlciBjbGlja3MgdGhlIGRyb3Bkb3duIGFjdGlvbiBpdGVtLiAgXG4gKiAgICR0YXJnZXQ6ICQoJyN0YXJnZXQtYnV0dG9uJyksIFxuICogICBcbiAqICAgLy8gVGFyZ2V0IGV2ZW50IG5hbWUgdG8gYmUgdHJpZ2dlcmVkLlxuICogICBldmVudDogJ2NsaWNrJywgICBcbiAqICAgXG4gKiAgIC8vIChvcHRpb25hbCkgUHJvdmlkZSBhIGZ1bmN0aW9uIHRvIG92ZXJyaWRlIHRoZSBkZWZhdWx0IGV2ZW50IGhhbmRsZXIuXG4gKiAgIGNhbGxiYWNrOiBjdXN0b21DYWxsYmFja0Z1bmMsIFxuICogICBcbiAqICAgLy8gKG9wdGlvbmFsKSBBZGQgYSBjdXN0b20gYWN0aW9uIHRpdGxlIGZvciB0aGUgZHJvcGRvd24gYnV0dG9uLlxuICogICB0aXRsZTogJ0FjdGlvbiBUaXRsZScgXG4gKiB9XG4gKiBcbiAqIGpzZS5saWJzLmFjdGlvbl9tYXBwZXIuYmluZChvcHRpb25zKTtcbiAqIGBgYFxuICpcbiAqIEJ5IGNsaWNraW5nIG9uIHRoZSBcIkJ1dHRvbiAxXCIgeW91IHdpbGwgcmVjZWl2ZSBhIFwiRnVuY3Rpb24gY2FsbGVkIVwiIGluIHRoZSBjb25zb2xlIVxuICpcbiAqIEBtb2R1bGUgQWRtaW4vTGlicy9hY3Rpb25fbWFwcGVyXG4gKiBAZXhwb3J0cyBqc2UubGlicy5hY3Rpb25fbWFwcGVyXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogVHJpZ2dlcnMgYSBzcGVjaWZpYyBldmVudCBmcm9tIGFuIGVsZW1lbnQuXG5cdCAqXG5cdCAqIFNvbWUgc2l0dWF0aW9ucyByZXF1aXJlIGEgZGlmZmVyZW50IGFwcHJvYWNoIHRoYW4ganVzdCB1c2luZyB0aGUgXCJ0cmlnZ2VyXCIgbWV0aG9kLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGVsZW1lbnQgRGVzdGluYXRpb24gZWxlbWVudCB0byBiZSB0cmlnZ2VyZWQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBFdmVudCBvcHRpb25zIGNhbiBiZSB1c2VkIGZvciBjcmVhdGluZyBuZXcgY29uZGl0aW9ucy5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfdHJpZ2dlckV2ZW50ID0gZnVuY3Rpb24oJGVsZW1lbnQsIGV2ZW50KSB7XG5cdFx0aWYgKCRlbGVtZW50LnByb3AoJ3RhZ05hbWUnKSA9PT0gJ0EnICYmIGV2ZW50LnR5cGUgPT09ICdjbGljaycpIHtcblx0XHRcdCRlbGVtZW50LmdldCgwKS5jbGljaygpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHQkZWxlbWVudC50cmlnZ2VyKGV2ZW50LnR5cGUpO1xuXHRcdH1cblx0fTtcblx0XG5cdC8qKlxuXHQgKiBCaW5kcyB0aGUgZXZlbnQgdG8gYSBuZXcgZHJvcGRvd24gYWN0aW9uIGl0ZW0uXG5cdCAqXG5cdCAqIEBwYXJhbSBvcHRpb25zIFNlZSBiaW5kIGRvY3VtZW50YXRpb24uXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX2JpbmRFdmVudCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHR2YXIgJGRyb3Bkb3duID0gb3B0aW9ucy4kZHJvcGRvd24sXG5cdFx0XHRhY3Rpb24gPSBvcHRpb25zLmFjdGlvbixcblx0XHRcdCR0YXJnZXQgPSBvcHRpb25zLiR0YXJnZXQsXG5cdFx0XHRldmVudE5hbWUgPSBvcHRpb25zLmV2ZW50LFxuXHRcdFx0Y2FsbGJhY2sgPSBvcHRpb25zLmNhbGxiYWNrIHx8IGZhbHNlLFxuXHRcdFx0dGl0bGUgPSBvcHRpb25zLnRpdGxlIHx8IChvcHRpb25zLiR0YXJnZXQubGVuZ3RoID8gb3B0aW9ucy4kdGFyZ2V0LnRleHQoKSA6ICc8Tm8gQWN0aW9uIFRpdGxlIFByb3ZpZGVkPicpLFxuXHRcdFx0JGxpID0gJCgnPGxpPjwvbGk+Jyk7XG5cdFx0XG5cdFx0JGxpLmh0bWwoJzxzcGFuIGRhdGEtdmFsdWU9XCInICsgYWN0aW9uICsgJ1wiPicgKyB0aXRsZSArICc8L3NwYW4+Jyk7XG5cdFx0JGRyb3Bkb3duLmZpbmQoJ3VsJykuYXBwZW5kKCRsaSk7XG5cdFx0XG5cdFx0JGxpLmZpbmQoJ3NwYW4nKS5vbihldmVudE5hbWUsIGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRpZiAoY2FsbGJhY2sgIT09IGZhbHNlKSB7XG5cdFx0XHRcdC8vZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0Ly9ldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFx0Y2FsbGJhY2suY2FsbCgkbGkuZmluZCgnc3BhbicpLCBldmVudCk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRfdHJpZ2dlckV2ZW50KCR0YXJnZXQsIGV2ZW50KTtcblx0XHRcdH1cblx0XHR9KTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBCaW5kcyB0aGUgZXZlbnRcblx0ICpcblx0ICogVGhpcyBtZXRob2QgaXMgdGhlIGluaXRpYWxpemluZyBwb2ludCBmb3IgYWxsIGV2ZW50IGJpbmRpbmdzLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBDb250YWlucyBhbGwgZWxlbWVudHMsIGZ1bmN0aW9uIGFuZCBldmVudCBkZXNjcmlwdGlvblxuXHQgKiBAcGFyYW0ge3N0cmluZ30gb3B0aW9ucy4kZHJvcGRvd24gU2VsZWN0b3IgZm9yIHRoZSBidXR0b24gZHJvcGRvd24gZWxlbWVudCAoZGl2KS5cblx0ICogQHBhcmFtIHtzdHJpbmd9IFtvcHRpb25zLiR0YXJnZXRdIChvcHRpb25hbCkgU2VsZWN0b3IgZm9yIHRoZSB0YXJnZXQgZWxlbWVudCBvZiB0aGUgbWFwcGluZy5cblx0ICogQHBhcmFtIHtzdHJpbmd9IG9wdGlvbnMuZXZlbnQgVGhlIG5hbWUgb2YgdGhlIGV2ZW50LiBUaGUgZXZlbnQgd2lsbCBiZSB0cmlnZ2VyZWQgb24gc291cmNlIGFuZFxuXHQgKiBkZXN0aW5hdGlvbiBlbGVtZW50IChlLmcuIFwiY2xpY2tcIiwgXCJtb3VzZWxlYXZlXCIpLlxuXHQgKiBAcGFyYW0ge2Z1bmN0aW9ufSBbb3B0aW9ucy5jYWxsYmFja10gKG9wdGlvbmFsKSBGdW5jdGlvbiB0aGF0IHdpbGwgYmUgY2FsbGVkIHdoZW4gdGhlIGV2ZW50IG9mIHRoZVxuXHQgKiBkZXN0aW5hdGlvbiBlbGVtZW50IGlzIHRyaWdnZXJlZC4gT1ZFUldSSVRFUyBUSEUgQUNUVUFMIEVWRU5UIEZPUiBUSEUgIERFU1RJTkFUSU9OIEVMRU1FTlQuXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB0aXRsZSAob3B0aW9uYWwpIFByb3ZpZGUgYW4gYWN0aW9uIHRpdGxlIGZvciB0aGUgZHJvcGRvd24gaWYgbm8gJHRhcmdldCB3YXMgZGVmaW5lZC5cblx0ICovXG5cdGV4cG9ydHMuYmluZCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHRfYmluZEV2ZW50KG9wdGlvbnMpO1xuXHR9O1xuXHRcbn0pKGpzZS5saWJzLmFjdGlvbl9tYXBwZXIpO1xuIl19
