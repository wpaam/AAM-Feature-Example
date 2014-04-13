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
 * Feature Example Controller
 * 
 * Each feature should have atleast one controller. This is the main class that
 * handle communication between AAM and feature. All filters and actions can be
 * defined in this controller.
 * 
 * Each controller must inherit AAM_Core_Extension class.
 * 
 * @package AAM
 * @author Vasyl Martyniuk <support@wpaam.com>
 * @copyright Copyright C  Vasyl Martyniuk
 * @license GNU General Public License {@link http://www.gnu.org/licenses/}
 */
class AAM_Feature_Example extends AAM_Core_Extension {

    /**
     * Unique Feature ID
     * 
     * Each Extension should have Unique ID or name that is used to mark the 
     * Extension. It is a good practise to have this ID defined in one place as
     * constant.
     */
    const FEATURE_ID = 'feature_example';

    /**
     * Constructor
     *
     * @param aam $parent Main AAM object
     *
     * @return void
     *
     * @access public
     * @see aam It is class in advanced-access-manager/aam.php file
     */
    public function __construct(aam $parent) {
        parent::__construct($parent);

         //include login counter object
        require_once(dirname(__FILE__) . '/counter.php');

        if (is_admin()) {
            $this->registerFeature();
        }

        //define new AAM Object. See aam_Control_Subject::getObject function for more
        //information about aam_object filter
        add_filter('aam_object', array($this, 'counterObject'), 10, 4);

        //login hooks
        add_action('wp_login', array($this, 'login'), 10, 2);
    }

    /**
     * Register feature
     * 
     * The Feature Example Extension has UI interface. That is why it is very 
     * important to make sure that it is backend (there is no need to load this 
     * Extension if user is browsing frontend).
     * 
     * From release 2.5, each Extension has its own capability assigned to it. By
     * default all Extensions have capability "Administrator" but this can be changed
     * with ConfigPress setting aam.feature.[you feature ID].capability.
     * For more information about this part of functionality chech official AAM
     * tutorial "AAM Super Admin" http://wpaam.com/tutorials/aam-super-admin/
     *
     * @return void
     *
     * @access protected
     */
    protected function registerFeature() {
        //add feature
        $capability = aam_Core_ConfigPress::getParam(
                'aam.feature.' . self::FEATURE_ID . '.capability', 
                'administrator'
        );

        //makre sure that current user has access to current Extension. This is 
        //mandatory check and should be obeyed by all developers
        if (current_user_can($capability)) {
            //register the Extension's javascript
            add_action('admin_print_scripts', array($this, 'printScripts'));
            //register the Extension's stylesheet
            add_action('admin_print_styles', array($this, 'printStyles'));
            //register the Feature
            aam_View_Collection::registerFeature((object) array(
                //uid is mandatory and this should be the unique ID
                'uid' => self::FEATURE_ID,
                //Extension Position is the list of AAM features. This works
                //the same way as WordPress Admin Menu
                'position' => 150,
                //Extension's Title
                'title' => __('Feature Example', 'aam'),
                //Define what subjects can see the Extenion's UI. AAM is based on 
                //idea of Subjects (Blog, Role, User & Visitor) and Objects (Post, 
                //Term, Event, Menu etc). This property defines what Subjects are
                //allowed to see this feature. So as Example if you click on Visitor
                //(on the Control Manager) you will not find Menu or Metabox Features.
                //This is because any visitor of your website does not have access to
                //backend so there is no need to show these features.
                'subjects' => array(
                    aam_Control_Subject_Role::UID,
                    aam_Control_Subject_User::UID,
                    aam_Control_Subject_Visitor::UID
                ),
                //Reference to Extension's Controller. Make sure that if your 
                //Extension display any UI, the controller property contains the 
                //instance of the Extenion's Controller and Controller has function
                //content. See aam_View_Manager::retrieveFeatures function for more
                //information
                'controller' => $this
            ));
        }
    }

    /**
     * Render UI Content
     * 
     * If Extension shows UI, this function is mandatory and should return the HTML
     * string.
     * 
     * @param aam_Control_Subject $subject Current Subject
     * 
     * @return string HTML Template
     * 
     * @access public
     * @see aam_View_Manager::retrieveFeatures
     */
    public function content(aam_Control_Subject $subject) {
        ob_start();
        require dirname(__FILE__) . '/ui.phtml';
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
    
    /**
     * Register Extension's javascript
     * 
     * This function should check if user is on AAM Page. Otherwise there is no need
     * to load any javascript to the header of HTML page.
     *
     * @return void
     *
     * @access public
     * @see aam::isAAMScreen
     */
    public function printScripts() {
        if ($this->getParent()->isAAMScreen()) {
            wp_enqueue_script(
                    'aam-feature-example-admin', 
                    AAM_FEATURE_EXAMPLE_BASE_URL . '/javascript.js', 
                    array('aam-admin') //make sure that main aam javascript is loaded
            );
        }
    }

    /**
     * Register Extenion's stylesheet
     *
     * This function should check if user is on AAM Page. Otherwise there is no need
     * to load any stylesheet to the header of HTML page.
     *
     * @return void
     *
     * @access public
     * @see aam::isAAMScreen
     */
    public function printStyles() {
        if ($this->getParent()->isAAMScreen()) {
            wp_enqueue_style(
                    'aam-feature-example-admin', 
                    AAM_FEATURE_EXAMPLE_BASE_URL . '/stylesheet.css', 
                    array('aam-style') //Extension can overwrite the main AAM style
            );
        }
    }

    /**
     * Retrieve the Login Counter Object
     * 
     * The filter aam_object is shared by all AAM Extensions that is why the check
     * if $object_id equals the Extension's Object. See the first if statement in 
     * this function.
     * 
     * @param null                $object     Default Object
     * @param int                 $object_uid Current User ID
     * @param string              $object_id  Request Object ID
     * @param aam_Control_Subject $subject    Current Subject
     * 
     * @return aam_Control_Object_LoginCounter
     * 
     * @access public
     * @see aam_Control_Subject::getObject
     */
    public function counterObject($object, $object_uid, $object_id, $subject) {
        if ($object_uid === aam_Control_Object_LoginCounter::UID) {
            $object = new aam_Control_Object_LoginCounter($subject, $object_id);
        }

        return $object;
    }

    /**
     * User Login Hook
     * 
     * This hook track the user's successfull login and increase the login counter.
     * 
     * @param string  $username User Login name
     * @param Wp_User $user     Current user object
     * 
     * @return void
     * 
     * @access public
     */
    public function login($username, $user) {
        //get current logged in Subject and increment the Login Counter
        $this->getParent()->getUser()
                ->getObject(aam_Control_Object_LoginCounter::UID)
                ->increment();
    }

}