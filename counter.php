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
 */

/**
 * Login Counter Object
 * 
 * Count how many times each use has been logged in to the Admin Backend.
 * All objects must inherit the abstract aam_Control_Object. This will force any
 * developer to implement mandatory methods and has also some useful methods to 
 * handle the AAM Object.
 * 
 * @package AAM
 * @author Vasyl Martyniuk <support@wpaam.com>
 * @copyright Copyright C  Vasyl Martyniuk
 * @license GNU General Public License {@link http://www.gnu.org/licenses/}
 */
class aam_Control_Object_LoginCounter extends aam_Control_Object {

    /**
     * Control Object UID
     * 
     * Every object should have its own unique ID that is used to retrieve the object
     */
    const UID = 'login_counter';

    /**
     * Object's option
     * 
     * In this sample the option is the login counter
     *
     * @var int
     *
     * @access private
     */
    private $_option = 0;

    /**
     * Initialize the Object
     * 
     * If Subject type is Role then get all users with current role and compute the
     * total ammount of logins. Otherwise if Subject is User, then just get user's
     * login_counter option from the usermeta database table
     *
     * @param int $object_id User's ID
     *
     * @return void
     *
     * @access public
     */
    public function init($object_id) {
        if ($this->getSubject()->getUID() == aam_Control_Subject_User::UID) {
            //get single user counter
            $option = $this->getSubject()->readOption(self::UID, $object_id, false);
        } else {
            //get all users in Role and combine the counters
            $query = new WP_User_Query(array(
                'number' => '',
                'blog_id' => get_current_blog_id(),
                'role' => $this->getSubject()->getId(),
                'fields' => 'id'
            ));
            $option = 0;

            foreach ($query->get_results() as $user) {
                $option += intval(get_user_option('aam_login_counter', $user));
            }
        }

        $this->setOption($option);
    }
    
    /**
     * Increment the login counter
     * 
     * Get current counter nubmer that already was retrieved in self::init function
     * and increment it by one.
     * 
     * @return void
     * 
     * @access public
     * @see self::init
     * @see self::save
     */
    public function increment(){
        $this->setOption($this->getOption() + 1);
        $this->save($this->getOption());
    }

    /**
     * Save the login counter
     * 
     * This function make sure that whatever is in _option private property will be
     * stored to the database table. This also take in consideration current Subject
     * type and save it to proper database table.
     *
     * @param int $counter
     *
     * @return void
     *
     * @access public
     */
    public function save($counter = null) {
        $this->getSubject()->updateOption($counter, self::UID);
    }

    /**
     * Cache object indicator
     * 
     * From release 2.2 AAM has caching mechanism. If this function returns true, it
     * means that AAM will cache current object.
     * The Caching mechanism is not active by default, but you can turn it on with
     * ConfigPress parameter aam.caching.
     * 
     * @return boolean
     * 
     * @access public
     */
    public function cacheObject() {
        return false;
    }

    /**
     * Get current Object UID
     * 
     * Return the unique Object's ID
     * 
     * @return string
     * 
     * @access public
     */
    public function getUID() {
        return self::UID;
    }

    /**
     * Set current login counter
     * 
     * @param int $option
     * 
     * @return void
     * 
     * @access public
     */
    public function setOption($option) {
        $this->_option = (is_scalar($option) ? $option : 0);
    }

    /**
     * Get login counter
     * 
     * @return int
     * 
     * @access public
     */
    public function getOption() {
        return $this->_option;
    }

}