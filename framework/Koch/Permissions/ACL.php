<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Permissions;

/**
 * Koch Framework - Class for Role and User Based Access Control Management
 *
 * @category    Koch
 * @package     Core
 * @subpackage  ACL
 */
class ACL
{
    /*
     * Roles Container
     * @var array
     */

    private static $roles = array();

    /*
     * Resources Container
     * @var array
     */
    private static $resources = array();

    /*
     * Rules Container
     * @var array
     */
    private static $rules = array();

    /*
     * Rules Container
     * @var array
     */
    private static $rulesOverflow = array();

    /*
     * Permission Container
     * @var array
     */
    private static $perms = array();

    /**
     * checkPermission
     *
     * Checks if the current user has a certain permission.
     *
     * Two values are necessary: the modulname and the name of the permission,
     * which is often the actionname.
     *
     * @param $modulename string The modulename, e.g. 'news'.
     * @param $permission string The permission name, e.g. 'action_show'.
     * @return boolean True if the user has the permission, false otherwise.
     */
    public static function checkPermission($module_name, $permission_name)
    {
        // if we got no modulname or permission, we have no access
        if ($module_name == '' or $permission_name == '') {
            return false;
        } else {
            // combine the module and permission name to a string
            $permission = '';
            $permission = $module_name . '.' . $permission_name;
        }

        $permissions = $_SESSION['user']['rights'];

        if (count($permissions) > 0) {
            foreach ($permissions as $key => $value) {
                if ($value == $permission) {
                    return true;
                }
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * createRightSession
     */
    public static function createRightSession($roleid, $userid = 0)
    {
        return self::getPermissions($roleid, $userid);
    }    

    /**
     * getRoleList
     *
     * Gives an array for column header or checkboxes
     *  e.g. if $title = false
     *    [1] = root
     *    [2] = admin
     *    [3] = member
     *    [4] = guest
     *    [5] = bot
     *  or if $title = true
     *    [1] = Supervisor
     *    [2] = Administrator
     *    [3] = User
     *    [4] = Guest
     *    [5] = Searchengine
     */
    public static function getRoleList($title = false)
    {
        if (false === $title) {
            $field = 'name';
        } else {
            $field = 'title';
        }

        foreach (self::$roles as $role) {
            $alist[] = $role[$field];
        }

        return $alist;
    }

    /**
     * getPermissions
     */
    private static function getPermissions($roleid, $userid = 0)
    {
        if ($roleid == '') {
            return '';
        }

        // initialize
        $permstring = '';
        $_perms = $uRules = array();

        // read acl-data
        $Actions = self::getAclDataActions();
        $Rules = self::getAclDataRules();
        if ($userid > 0) {
            $uRules = self::getAclDataURules($userid);
        }

        // prepare actions
        foreach ($Actions as $act) {
            $_actions[$act['action_id']] = $act['modulname'] . '.' . $act['action'];
        }

        // create permission array only for the given role_id
        foreach ($Rules as $rule) {
            if ($rule['role_id'] == $roleid) {
                $_perms[$_actions[$rule['action_id']]] = 1;
            }
        }

        // create/overide group-permissions width user-permissions
        if ($userid > 0) {
            if (count($uRules) > 0) {
                // @todo
            }
        }

        // prepare permissionstring for session
        foreach ($_perms as $key => $value) {
            $permstring .= $key . ',';
        }

        $permstring = mb_substr($permstring, 0, strlen($permstring) - 1);
        #\Koch\Debug\Debug::printR($permstring);

        return $permstring;
    }

    /**
     * createAclDataRoles
     */
    private static function createAclDataRoles()
    {
        $roles = Doctrine_Query::create()
            ->select('r.*')
            ->from('CsAclRoles r')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->orderby('r.sort DESC')
            ->execute(array());

        return $roles;
    }

    /**
     * getAclDataActions
     */
    private static function getAclDataActions()
    {
        $actions = Doctrine_Query::create()
            ->select('a.*')
            ->from('CsAclActions a')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->orderby('a.modulname ASC')
            ->execute(array());

        return $actions;
    }

    /**
     * getAclDataRules
     */
    private static function getAclDataRules()
    {
        $rules = Doctrine_Query::create()
            ->select('u.*')
            ->from('CsAclRules u')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->where('u.access = 1')
            ->orderby('u.action_id ASC')
            ->execute(array());

        return $rules;
    }

    /**
     * getAclDataURules
     */
    private static function getAclDataURules()
    {
        $urules = Doctrine_Query::create()
            ->select('r.*')
            ->from('cs_acl_urules r')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->orderby('r.module_id ASC')
            ->execute(array());

        return $urules;
    }
}
