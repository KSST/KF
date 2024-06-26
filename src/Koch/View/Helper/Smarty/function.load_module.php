<?php
/**
 * Koch Framework Smarty Viewhelper.
 */

/**
 * Name:    loadmodule
 * Type:    function
 * Purpose: This smarty function loads a certain module action and inserts it's content.
 *
 * Static Function to Call variable Methods from templates via
 * {load_module name= sub= params=}
 * Parameters: name, sub, action, params, items
 *
 * Example:
 * {load_module name="quotes" action="widget_quotes"}
 *
 * @param array  $params as described above (emmail, size, rating, defaultimage)
 * @param Smarty $smarty
 *
 * @return string
 */
function Smarty_function_load_module($params, $smarty)
{
    // init incomming variables
    $module     = isset($params['name']) ? (string) $params['name'] : '';
    $controller = isset($params['ctrl']) ? (string) $params['ctrl'] : '';
    $action     = isset($params['action']) ? (string) $params['action'] : '';
    $items      = isset($params['items']) ? (int) $params['items'] : null;

    // Load Module/Controller in order to get access to the widget method
    //$module_path = \Koch\Mvc\Mapper::getModulePath($module);
    //echo $module_path . '<br>';

    $classname = \Koch\Mvc\Mapper::mapControllerToClassname($module, $controller);

    if (class_exists($classname) === false) {
        return '<br/>Widget Loading Error. <br/>Module missing or misspelled? (' . $module . ' > ' . $controller . ')';
    }

    // Instantiate Class
    $module_controller = new $classname(new \Koch\Http\HttpRequest(), new \Koch\Http\HttpResponse());
    $module_controller->setView($smarty);
    //$module_controller->setModel($module);

    /*
     * Get the Ouptut of the Object->Method Call
     */
    if (method_exists($module_controller, $action)) {

        // special handling of adminmenu
        // @todo remove this, find a way to pass params with context
        if ($classname === 'application_module_menu_admin') {
            $items = [];

            if (empty($params['params'])) {
                $items = null;
            }

            // build array from string "param|param|etc"
            $items = explode('\|', (string) $params['params']);

            return $module_controller->$action($items);
        }

        // Call the Action on the Module
        $module_controller->$action($items);

        /*
         * Output the template of a widget
         *
         * The template is fetched from the module or from the various theme folders!
         * You can also set an alternative widgettemplate inside the widget itself
         * via setTemplate() method.
         *
         * For the order of template detection   method.
         * @see \Koch\View\Mapper->getModuleTemplatePaths()
         */
        // build template name
        $template = $action . '.tpl';

        $viewMapper = new Koch\View\Mapper();
        $template   = $viewMapper->getModuleTemplatePath($template, $module);

        if (is_file($template)) {
            return $smarty->fetch($template);
        }

        $errorMsg = 'Widget Template not found.';
    }

    $errorMsg = 'Module Action not found.';

    return trigger_error($errorMsg . '<br /> ' . $classname . ' -> ' . $action . '(' . $items . ')');
}
