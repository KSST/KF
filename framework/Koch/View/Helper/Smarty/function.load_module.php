<?php
/**
 * Koch Framework Smarty Viewhelper
 *
 * @category Koch
 * @package Smarty
 * @subpackage Viewhelper
 */

/**
 * Name:         loadmodule
 * Type:         function
 * Purpose: This TAG inserts the a certain module and its widget.
 *
 * Static Function to Call variable Methods from templates via
 * {load_module name= sub= params=}
 * Parameters: name, sub, action, params, items
 *
 * Example:
 * {load_module name="quotes" action="widget_quotes"}
 *
 * @param array $params as described above (emmail, size, rating, defaultimage)
 * @param Smarty $smarty
 * @return string
 */
function Smarty_function_load_module($params, $smarty)
{
    // debug display for the incomming parameters of a specific load_module request
    if ($params['name'] == 'news') {
        // \Koch\Debug\Debug::firebug($params);
    }

    // Init incomming Variables
    $module = isset($params['name']) ? (string) $params['name'] : '';
    $controller = isset($params['ctrl']) ? (string) $params['ctrl'] : '';
    $action = isset($params['action']) ? (string) $params['action'] : '';
    $items = isset($params['items']) ? (int) $params['items'] : null;

    // Load Module/Controller in order to get access to the widget method
    $module_path = \Koch\Mvc\Mapper::getModulePath($module);
    #echo $module_path . '<br>';

    $classname = \Koch\Mvc\Mapper::mapControllerToClassname($module, $controller);
    #echo $classname . '<br>';

    if (class_exists($classname) === false) {
        return '<br/>Widget Loading Error.<br/>Module missing or misspelled? <strong>' . $module .' ' . $controller . '</strong>';
    }

    // Instantiate Class
    $module_controller = new $classname(
                \Clansuite\Application::getInjector()->instantiate('Koch\Http\HttpRequest'),
                \Clansuite\Application::getInjector()->instantiate('Koch\Http\HttpResponse')
    );
    $module_controller->setView($smarty);
    //$module_controller->setModel($module);

    /**
     * Get the Ouptut of the Object->Method Call
     */
    if (method_exists($module_controller, $action)) {
        // exceptional handling of parameters and output for adminmenu
        if ($classname == 'clansuite_module_menu_admin') {
            $parameters = array();

            // Build a Parameter Array from Parameter String like: param|param|etc
            if (empty($params['params'])) {
                $parameters = null;
            } else {
                $parameters = explode('\|', $params['params']);
            }

            return $module_controller->$action($parameters);
        }

        // Call the Action on the Module
        $module_controller->$action($items);

        /**
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
        $template = $viewMapper->getModuleTemplatePath($template, $module);

        \Koch\Debug\Debug::printR($template);

        if(is_file($template)) {
            return $smarty->fetch($template);
        } else {
            return trigger_error('Widget Template not found. <br /> ' . $classname . ' -> ' . $action . '(' . $items . ')');
        }
    }
}
