<?php

/**
 * Smarty plugin.
 */

/**
 * Smarty {columnsort} function plugin.
 *
 * Type:     function
 * Name:     columnsort
 * Purpose:  easy sorting of a html table by columns
 *
 * @param array parameters (cid, html, selected_class, id, asc_image, desc_image)
 * @param Smarty
 *
 * @return string|null
 */
function smarty_function_columnsort($params, $smarty)
{
    $selected_class = null;
    $current_id     = 0;
    $SMCS_id        = 'default';
    //static $sort_asc_image = null;
    //static $sort_desc_image = null;
    $sort_asc_image  = WWW_ROOT_THEMES_CORE . 'images/icons/asc.png';
    $sort_desc_image = WWW_ROOT_THEMES_CORE . 'images/icons/desc.png';

    if ($params['cid'] !== null) {
        if ($SMCS_id !== $params['cid']) {
            $current_id = 0;
        }

        $SMCS_id = $params['cid'];
    }

    // Retrieve the $_SESSION columnsort object.
    if (!isset($_SESSION['SmartyColumnSort'][$SMCS_id])) {
        trigger_error('columnsort: SmartyColumnSort.class.php needs to be included for columnsort to work.');

        return;
    }
    $columnsort = $_SESSION['SmartyColumnSort'][$SMCS_id];

    // HTML
    if (!isset($params['html'])) {
        trigger_error('columnsort: missing "html" parameter.');

        return;
    }

    /*
      if ($params['translate'] != 0) {
      $params['html'] = _($params['html']);
      } */
    $html = $params['html'];

    // selected_class
    if ($params['selected_class'] !== null) {
        $selected_class = $params['selected_class'];
    }

    // ID for column table
    if ($params['id'] !== null) {
        $id = $params['id'];

        // Increase current id with 1 to prepare for next value
        $current_id = $id + 1;
    } else {
        $id = $current_id++;
    }

    /* disabled
      if (($params['asc_image'] !== null) && ($params['desc_image'] !== null)) {
      // Set asc and desc sort images (will be placed after the sorted column)
      $sort_asc_image = $params['asc_image'];
      $sort_desc_image = $params['desc_image'];
      } elseif ($params['asc_image']) || isset($params['desc_image'] !== null) {
      trigger_error('columnsort: Both "asc_image" and "desc_image" needs to be present, or none of them.');
      }
     */

    // alt for image
    if ($params['img_alt'] !== null) {
        $img_alt = $params['img_alt'];
    } else {
        $img_alt = '';
    }

    // Get current sort order for current column id
    $sort_order = _smarty_columnsort_sort_order($id, $columnsort['column_array'], $columnsort['default_sort'], $smarty);

    if ($sort_order === false) {
        trigger_error('columnsort: too few columns in translate table!');

        return;
    }

    // The column is selected if the get vars exists and is the current column OR
    // if the get vars does not exist and the current column is default.
    if ($columnsort['current_column'] !== null and $columnsort['current_column'] === $id) {
        $selected = true;
    }

    // Reverse sort order for the output.
    if ($columnsort['current_sort']) {
        $sort_order = mb_strtolower((string) $columnsort['current_sort']) === 'asc' ? 'desc' : 'asc';
    } elseif ($columnsort['current_column'] === null and $id === $columnsort['default_column']) {
        $selected = true;

        // Reverse sort order for the output.
        $sort_order = $sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        $selected = false;
    }

    $columnsort['target_page'] .= (mb_strpos((string) $columnsort['target_page'], '?') !== false ? '&' : '?');

    $url = $columnsort['target_page'] . $columnsort['column_var'] . "=$id&" . $columnsort['sort_var'] . "=$sort_order";

    // XML compliance patch by Vaccafoeda
    $url = str_replace('&', '&amp;', $url);

    // If asc/desc image exists, append it.
    if ($selected && $sort_asc_image !== null) {
        $image_src = $sort_order === 'asc' ? $sort_desc_image : $sort_asc_image;
        $image     = " <img src=\"$image_src\" alt=\"$img_alt\" border=\"0\" />";
    } else {
        $image = '';
    }

    $class = $selected && $selected_class ? "class=\"$selected_class\"" : '';

    $result = '<a ' . $class . ' href="' . $url . '>' .
        '<span style="width:100%;padding:0px;margin:0px;text-align:center;">' .
        $html . ' ' . $image . '</span></a>';

    return $result;
}

/**
 * @param int $id
 */
function _smarty_columnsort_sort_order($id, $columns, $default_sort, $smarty)
{
    if (!isset($columns[$id])) {
        return false;
    }

    if (!is_array($columns[$id])) {
        return $default_sort;
    }

    if (count($columns[$id]) !== 2) {
        trigger_error('columnsort: column array must be array("value", "asc|desc")');

        return false;
    }

    return $columns[$id][1];
}
