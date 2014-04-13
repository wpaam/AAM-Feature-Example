<?php

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
 
 * =================================================================================
 * This is the bootstrap file for the extension. When AAM loads it all Extensions
 * inside the folder advanced-access-manager/extension. See aam::loadExtensions
 * function to understand when Extensions are loaded.
 * 
 * AAM literally scan the extension folder and load the Extension if index.php file
 * exists. See aam_Core_Repository::load function to get more information about this
 * process.
 * =================================================================================
 */

//define Extension Base URL for UI load. If Extension does not UI, there is no needs
//to define this constant
$dirname = basename(dirname(__FILE__));
define('AAM_FEATURE_EXAMPLE_BASE_URL', AAM_BASE_URL . 'extension/' . $dirname);

//load the Extension Controller
require_once dirname(__FILE__) . '/extension.php';

//instantiate and return the Extension controller. Controller is cached inside the
//aam_Core_Repository so singleton pattern is involved here.
//getParent function returns the main AAM controller inside the 
//advanced-access-manager/aam.php file
return new AAM_Feature_Example($this->getParent());