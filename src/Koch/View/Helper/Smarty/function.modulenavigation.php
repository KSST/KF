<?php
/**
 * Smarty plugin.
 */

/**
 * Smarty ModuleNavigation
 * Displays a Module Navigation Element
 * depends on module configuration file.
 *
 * Examples:
 * <pre>
 * {modulnavigation}
 * </pre>
 *
 * Type:     function<br>
 * Name:     modulenavigation<br>
 * Purpose:  display modulenavigation<br>
 *
 * @param array  $params
 * @param Smarty $smarty
 *
 * @return string
 */
function Smarty_function_modulenavigation($params, $smarty)
{
    $module = (new \Koch\Http\HttpRequest())->getRoute()->getModule();

    $file = APPLICATION_MODULES_PATH . $module . DIRECTORY_SEPARATOR . $module . '.menu.php';

    if (is_file($file)) {
        // this includes the file, which contains a php array name $modulenavigation
        include $file;

        // push the $modulenavigation array to a callback function
        // for further processing of the menu items
        $modulenavigation = array_map('applyCallbacks', $modulenavigation);

        $smarty->assign('modulenavigation', $modulenavigation);

        // The file is located in /themes/core/view/smarty/modulenavigation-generic.tpl
        return $smarty->fetch('modulenavigation-generic.tpl');
    } else { // the module menu navigation file is missing
        $smarty->assign('modulename', $module);
        $errormessage = $smarty->fetch('modulenavigation_not_found.tpl');
        trigger_error($errormessage);
    }
}

/**
 * array_map callback function.
 *
 * 1) convert short urls
 * 2) execute callback conditions of menu items
 * 3) use name as title, if title is not defined
 */
function applyCallbacks(array $modulenavigation)
{
    /*
     * 1) Convert Short Urls
     *
     * This replaces the values of the 'url' key (array['url']),
     * because these might be shorthands, like "/index/show".
     */
    $modulenavigation['url'] = \Koch\Router\Router::buildURL($modulenavigation['url']);

    /*
     * 2) Conditions of menu items
     *
     * If the condition of the menu item is not met,
     * then condition is set to false, otherwise true.
     */
    if ($modulenavigation['condition'] !== null) {
        /*
         * the if statement evaluates the content of the key condition
         * and compares it to false, then reassigns the boolean value as
         * the condition value.
         *
         * for now you might define 'condition' => extension_loaded('apc')
         *
         * @todo check usage of closures
         */
        if ($modulenavigation['condition'] === false) {
            $modulenavigation['condition'] = false;
        } else {
            $modulenavigation['condition'] = true;
        }
    }

    /*
     * 3) use name as title, if title is not defined
     */
    if ($modulenavigation['title'] === '') {
        $modulenavigation['title'] = $modulenavigation['name'];
    }

    return $modulenavigation;
}
