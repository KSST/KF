<?php

/**
 * Smarty plugin
 */

/**
 * Smarty Viewhelper for checking the user authorization for a resource.
 *
 * Usage Example:
 * <pre>
 * {is_authorized name="module.action"}
 * </pre>
 *
 * Type:    function<br>
 * Name:    is_authorized<br>
 * Purpose: Checks, if a user is authorized for accessing a resource.<br>
 *
 * @param   array $params
 * @param   Smarty $smarty
 * @return  boolean True if user has permission, false otherwise.
 */
function Smarty_function_is_authorized($params)
{
    // ensure we got parameter name
    if (empty($params['name']) or is_string($params['name']) == false) {
        trigger_error(
            'Parameter "name" is not a string or empty. Please provide a name in the format "module.action".'
        );

        return;
    }

    // ensure parameter name contains a dot
    if (false === strpos($params['name'], '.')) {
        trigger_error(
            'Parameter "name" is not in the correct format. Please provide a name in the format "module.action".'
        );

        return;
    } else { // we got a permission name like "news.action_show"
        // split string by delimiter string
        $array      = explode('.', $params['name']);
        $module     = $array[0];
        $permission = $array[1];
    }

    // perform the permission check
    if (false !== \Koch\User\Authorization::isAuthorized($module, $permission)) {
        unset($array, $name, $permission);

        return true;
    }

    return false;
}
