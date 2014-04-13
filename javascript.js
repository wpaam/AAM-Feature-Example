/**
  Copyright (C) Vasyl Martyniuk <support@wpaam.com>

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Add Feature Example funciton to AAM prototype object
 * 
 * @returns void
 */
AAM.prototype.featureExample = function() {
    //Send Email to Us
    jQuery('.feature-example-message-action').bind('click', function(event) {
        event.preventDefault();
        jQuery('#aam_message').trigger('click');
    });
};

//run the javascript on page complete load
jQuery(document).ready(function() {
    //register aam javascript action
    aamInterface.addAction('aam_init_features', function() {
        aamInterface.featureExample();
    });
});