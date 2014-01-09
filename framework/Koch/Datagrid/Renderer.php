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

namespace Koch\Datagrid;

/**
 * Datagrid Renderer
 *
 * Generates html-code based upon the grid settings.
 */
class Renderer
{
    /**
     * The datagrid
     *
     * @var Koch\Datagrid\Datagrid $_Datagrid
     */
    private static $datagrid;

    /**
     * The look of the links of the pager
     *
     * @var string
     */
    // static
    /**
     * The items for results per page
     *
     * @var array
     */
    private static $resultsPerPageItems = array(5, 10, 20, 50, 100);

    /**
     * Instantiate renderer and attach Datagrid to it
     *
     * @param Koch\Datagrid\Datagrid $_Datagrid
     */
    public function __construct($_Datagrid)
    {
        $this->setDatagrid($_Datagrid);

        return $this;
    }

    /**
     * Set the datagrid object
     *
     * @param Koch\Datagrid\Datagrid $_Datagrid
     */
    public static function setDatagrid($_Datagrid)
    {
        self::$datagrid = $_Datagrid;
    }

    /**
     * Get the Datagrid object
     *
     * @return Koch\Datagrid\Datagrid $_Datagrid
     */
    public static function getDatagrid()
    {
        return self::$datagrid;
    }

    /**
     * Set the items for the dropdownbox of results per page
     *
     * @param array $_Items
     */
    public static function setResultsPerPageItems(array $_Items)
    {
        self::$resultsPerPageItems = $_Items;
    }

    public static function renderPager()
    {
        $currentPage = $pagination->getCurrentPage();
        $results_count = $pagination->getTotalResultsCount();
        $results_per_page = $pagination->getResultsPerPage();
        $numberOfPages = $pagination->getNumberOfPages();

        $min_page_number = (int) ($currentPage - 5);
        $max_page_number = (int) ($currentPage + 5);

        if ($min_page_number < 1) {
            $min_page_number = 1;
            $max_page_number = 11;
            $max_page_number = ($max_page_number > $numberOfPages) ? $numberOfPages : 11;
        }

        if ($max_page_number > $numberOfPages) {
            $max_page_number = $numberOfPages;
            $min_page_number = $max_page_number - 10;
            $min_page_number = ($min_page_number < 1) ? 1 : $max_page_number - 10;
        }

        if ($results_count <= $results_per_page) {
            $min_page_number = 1;
            $max_page_number = $numberOfPages;
        }

        // init
        $pages = $first = $prev = $next = $last = '';

        // pager render modes:
        // a) [1][2][3][4][5]
        // b) first [1][2][3][4][5] last
        // c) first prev [1][2][3][4][5] next last

        if ($currentPage > 1 and $numberOfPages > 1) {
            $url = self::getURLForPageInRange(self::getFirstPage());
            $first = '<li class="previous"><a href="' . $url . '" title="First Page">&laquo First Page</a></li>';

            $url = self::getURLForPageInRange(self::getPreviousPage());
            $prev = '<li class="previous"><a href="' . $url . '" title="Previous Page">&laquo Previous</a></li>';
        }

        // renderPageRangeAroundPage

        if ($currentPage < $numberOfPages and $numberOfPages > 1) {

            $url = self::getURLForPageInRange(self::getNextPage());
            $next = '<li class="next"><a href="' . $url . '" title="Next Page">Next &raquo;</a></li>';

            $url = self::getURLForPageInRange(self::getLastPage());
            $last = '<li class="next"><a href="' . $url . '" title="Last Page">Last Page &raquo;</a></li>';
        }

        /**
         * Internal Debug Display for the pager.
         */
        $info = '';
        $info .= 'Total Records' . $results_count;
        $info .= 'Number of pages' . $numberOfPages;
        $info .= 'Current Page' . $currentPage;
        $info .= 'Min Page Index' . $min_page_number;
        $info .= 'Max Page Index' . $max_page_number;
        $info .= 'Results per page' . $results_per_page;
        #\Koch\Debug\Debug::firebug($info);

        $html = '';
        $html .= '<ul class="pagination3">';
        // $html .= self::getPaginationCSSdynamically();
        $html .= "$first$prev$pages$next$last";
        $html .= '</ul>';

        return $html;
    }

    /**
     * Inserts a javascript, which will insert "pagination.css"
     * to the head of the page, after(!) the page is ready.
     *
     * @return string
     */
    public static function getPaginationCSSdynamically()
    {
        $html = '<script>$(document).ready(function () {';
        $html .= '$("<link/>", {
                       rel: "stylesheet",
                       type: "text/css",
                       href: "themes/core/css/pagination.css"
                    }).appendTo("head");';
        $html .= '});</script>';

        return $html;
    }

    public static function getURLForPageInRange($page)
    {
        $url = self::getDatagrid()->getBaseURL();
        $alias = self::getDatagrid()->getParameterAlias('Page');

        if (defined('MOD_REWRITE')) {
            $url .= '/' . $alias . '/' . $page;
        } else {
            $url .= '&' . $alias . '=' . $page;
        }

        return $url;
    }

    /**
     * Get the items for the dropdownbox of results per page
     *
     * @return string
     */
    public static function getResultsPerPageItems()
    {
        return self::$resultsPerPageItems;
    }

    /**
     * Render the datagrid table
     *
     * @param string The html-code for the table
     * @return string Returns the html-code of the datagridtable
     */
    private static function renderTable()
    {
        $table_sprintf = '<table class="DatagridTable DatagridTable-%s"';
        $table_sprintf .= ' cellspacing="0" cellpadding="0" border="0" id="%s">' . CR;
        $table_sprintf .= CR . '%s' . CR ;
        $table_sprintf .= '</table>' . CR;

        $tableContent = '';
        $tableContent .= self::renderTableCaption();
        $tableContent .= self::renderTableBody('one');   // search + pagination
        $tableContent .= self::renderTableHeader();      // this isn't a <thead> tag, but <tbody>
        $tableContent .= self::renderTableBody('two');
        $tableContent .= self::renderTableBody('three');
        $tableContent .= self::renderTableFooter();

        $html = sprintf($table_sprintf, self::getDatagrid()->getAlias(), self::getDatagrid()->getId(), $tableContent);

        return $html;
    }

    /**
     * Render the label
     *
     * @return string Returns the html-code for the label if enabled
     */
    private static function renderLabel()
    {
        if (self::getDatagrid()->isEnabled('Label')) {
            $html = '<div class="DatagridLabel DatagridLabel-' . self::getDatagrid()->getAlias() . '">' . CR;
            $html .= self::getDatagrid()->getLabel() . CR . '</div>';

            return $html;
        }
    }

    /**
     * Render the description
     *
     * @return string Returns the html-code for the description
     */
    private static function renderDescription()
    {
        if (self::getDatagrid()->isEnabled('Description')) {
            $html = '<div class="DatagridDescription DatagridDescription-' . self::getDatagrid()->getAlias();
            $html .= '">' . CR . self::getDatagrid()->getDescription() . CR . '</div>';

            return $html;
        }
    }

    /**
     * Render the caption
     *
     * @return string Returns the html-code for the caption
     */
    private static function renderTableCaption()
    {
        $html = '';

        if (self::getDatagrid()->isEnabled('Caption')) {
            $html .= '<caption>';
            #$html .= self::getDatagrid()->getCaption();
            $html .= self::renderLabel();
            $html .= self::renderDescription();
            $html .= '</caption>' . CR;
        }

        return $html;
    }

    /**
     * Represents a sortstring for a-Tags
     *
     * @param string SortKey
     * @param string SortOrder
     * @return string Returns a string such as index.php?mod=news&action=admin&sortc=Title&sorto=DESC
     */
    private static function getURLStringWithSorting($_SortColumn, $_SortOrder)
    {
        $url_string = sprintf(
            '?%s=%s&%s=%s',
            self::getDatagrid()->getParameterAlias('SortColumn'),
            $_SortColumn,
            self::getDatagrid()->getParameterAlias('SortOrder'),
            $_SortOrder
        );

        return self::getDatagrid()->appendUrl($url_string);
    }

    /**
     * Render the header
     *
     * @return string Returns the html-code for the header
     */
    private static function renderTableHeader()
    {
        $html = '';

        if (self::getDatagrid()->isEnabled('Header')) {
            $html .= '<tbody>' . CR; // @todo OH MY GODDON! <thead> is not working here
            $html .= '<tr>' . CR;
            $html .= self::renderTableRowsHeader();
            $html .= '</tr>' . CR;
            $html .= '</tbody>' . CR; // @todo OMG^2
        }

        return $html;
    }

    /**
     * Render the header of the rows
     *
     * @return string Returns the html-code for the rows-header
     */
    private static function renderTableRowsHeader()
    {
        $html = '';

        foreach (self::getDatagrid()->getColumns() as $column) {
            $html .= self::renderTableColumn($column);
        }

        return $html;
    }

    /**
     * Render the pagination for the datagrid
     *
     * @param  boolean $_ShowResultsPerPage If true, the drop-down for maximal results per page is shown.
     *                                      Otherwise the total number of items.
     * @return string  Returns the html-code for the pagination row
     */
    private static function renderTablePagination($_ShowResultsPerPage = true)
    {
        #\Koch\Debug\Debug::printR('Pagination: ' . self::renderPager());
        $html = '';
        if (self::getDatagrid()->isEnabled('Pagination')) {
            $html .= '<tr>';
            #$html .= '<td colspan="1">';
            #$html .= _('Page: ');
            #$html .= '</td>';
            $html .= '<td colspan="' . (self::getDatagrid()->getColumnCount()) . '">';

            $html .= self::renderPager();

            // results per page drop down
            if ($_ShowResultsPerPage) {
                $html .= '<div class="ResultsPerPage">';
                $html .= '<select name="' . self::getDatagrid()->getParameterAlias('ResultsPerPage') . '"';
                $html .= ' onchange="this.form.submit();">';
                $_ResultsPerPageItems = self::getResultsPerPageItems();
                foreach ($_ResultsPerPageItems as $item) {
                    $html .= '<option value="' . $item . '" ';
                    $html .= ((self::getDatagrid()->getResultsPerPage() == $item) ? 'selected="selected"' : '');
                    $html .= '>' . $item . '</option>';
                }
                $html .= '</select>';
                $html .= '</div>';
            } else { // show total number of items in results set
                $html .= '<div class="ResultsPerPage">';
                $html .= self::getDatagrid()->getTotalResultsCount() . _(' items');
                $html .= '</div>';
            }

            $html .= '</td></tr>';
        }

        return $html;
    }

    /**
     * Renders the table body.
     *
     * Render Types:
     * One:     will render Search + Pagination
     * Two:     will render Table Rows
     * Three:   will render Batch Actions + Pagination
     *
     * @see renderTable()
     * @param $type Render type toggle (one, two, three)
     * @return string Returns the html-code for the table body
     */
    private static function renderTableBody($type = 'one')
    {
        $html = '';

        if ($type == 'one') {
            #$html .= self::renderTableActions();
            $html .= self::renderTableSearch();
            $html .= self::renderTablePagination();
        }

        if ($type == 'two' or $type == 'three') {
            $html .= '<tbody>';

            if ($type == 'two') {
                #$html .= self::renderTableActions();
                $html .= self::renderTableRows();
            }

            if ($type == 'three') {
                $html .= self::renderTableBatchActions();
                $html .= self::renderTablePagination(false);
            }

            $html .= '</tbody>';
        }

        return $html;
    }

    /**
     * Render the actions of the rows
     *
     * @return string Returns the html-code for the actions
     */
    private static function renderTableBatchActions()
    {
        $_BatchActions = self::getDatagrid()->getBatchActions();
        $html = '';

        if (count($_BatchActions) > 0 && self::getDatagrid()->isEnabled('BatchActions')) {
            $config = null;
            $config = Clansuite_CMS::getInjector()->instantiate('Clansuite_Config')->toArray();

            $html .= '<tr>';
            $html .= '<td class="DatagridBatchActions"><input type="checkbox" class="DatagridSelectAll" /></td>';

            $html .= '<td colspan=' . (self::getDatagrid()->getColumnCount() - 1) . '>';
            $html .= '<select name="action" id="BatchActionId">';
            $html .= '<option value="' . $config['defaults']['action'] . '">' . _('(Choose an action)') . '</option>';
            foreach ($_BatchActions as $BatchAction) {
                $html .= '<option value="' . $BatchAction['Action'] . '">' . $BatchAction['Name'] . '</option>';
            }
            $html .= '</select>';
            $html .= '<input class="ButtonOrange" type="submit" value="' . _('Execute') . '" />';
            $html .= '</td>';

            $html .= '</tr>';
        }

        return $html;
    }

    /**
     * Render all the rows
     *
     * @return string Returns the html-code for all rows
     */
    private static function renderTableRows()
    {
        $html = '';
        $rowKey = null;

        $rows = self::getDatagrid()->getRows();

        $i = 0;
        foreach ($rows as $rowKey => $row) {
            $i++;
            // @todo consider removing the css alternating code, in favor of css3 tr:nth-child
            $html .= self::renderTableRow($row, !($i % 2));
        }

        // render a "no results" row
        if ($html == '') {
            $html .= '<tr class="DatagridRow DatagridRow-NoResults">';
            $html .= '<td class="DatagridCell DatagridCell-NoResults"';
            $html .= ' colspan="' . self::getDatagrid()->getColumnCount() . '">';
            $html .= _('No Results');
            $html .= '</td>';
            $html .= '</tr>';
        }

        unset($rowKey, $rows, $i, $row);

        return $html;
    }

    /**
     * Render a single row
     * HTML: <tr>(.*)</tr>
     *
     * @param $row Koch\Datagrid\Datagrid_Row
     * @param $alternate row alternating toggle
     * @return string Returns the html-code for a single row
     */
    private static function renderTableRow($row, $alternate)
    {
        $_alternateClass = '';

        if ($alternate === true) {
            $_alternateClass = 'Alternate';
        }

        // @todo consider removing the css alternating code, in favor of css3 tr:nth-child
        $html = null;
        $html = '<tr class="DatagridRow DatagridRow-' . $row->getAlias() . ' ' . $_alternateClass . '">';

        $cells = $row->getCells();
        foreach ($cells as $oCell) {
            $html .= self::renderTableCell($oCell);
        }

        $html .= '</tr>';

        unset($cells, $_alternateClass);

        return $html;
    }

    /**
     * Render a single cell
     * * HTML: <td>(.*)</td>
     *
     * @param Koch\Datagrid\Datagrid_Cell
     * @return string Return the html-code for the cell
     */
    private static function renderTableCell($_oCell)
    {
        $html = '';
        $html .= '<td class="DatagridCell DatagridCell-Cell_' . $_oCell->getColumn()->getPosition() . '">';
        $html .= $_oCell->render() . '</td>';

        return $html;
    }

    /**
     * Renders a column
     *
     * <th>ColumnOne</th>
     *
     * - Id Tag
     * - Common and individual Css class tag
     * - displays column name
     * - if sorting is enabled, displayes sort order toggle
     * - sort order toggle is text or icon
     *
     * @param Koch\Datagrid\Datagrid_Column
     * @return string Returns the html-code for a single column
     */
    private static function renderTableColumn($columnObject)
    {
        $html = '';
        $html .= '<th id="ColHeaderId-' . $columnObject->getAlias() . '"';
        $html .= ' class="ColHeader ColHeader-' . $columnObject->getAlias() . '">';
        $html .= $columnObject->getName();

        if ($columnObject->isEnabled('Sorting')) {
            $html .= '&nbsp;<a href="';
            $html .= self::getURLStringWithSorting(
                $columnObject->getAlias(),
                self::getDatagrid()->getSortDirectionOpposite($columnObject->getSortOrder())
            );
            $html .= '">';
            $html .= _($columnObject->getSortOrder());
            $html .= '</a>';
        }

        $html .= '</th>';

        return $html;
    }

    /**
     * Render the footer
     *
     * @return string Returns the html-code for the footer
     */
    private static function renderTableFooter()
    {
        if (self::getDatagrid()->isEnabled('Footer')) {
            $html = '';
            $html .= '<tfoot>' . CR;
            // @todo getter for footer html
            $html .= '</tfoot>' . CR;

            return $html;
        } else {
            return; #'<tfoot>&nsbp;</tfoot>';
        }
    }

    /**
     * Renders the search element of the table
     *
     * @return $string HTML Representation of the Table Search Element
     */
    private static function renderTableSearch()
    {
        $html = '';

        if (self::getDatagrid()->isEnabled('Search')) {

            $value = htmlentities($_SESSION['Datagrid_' . self::getDatagrid()->getAlias()]['SearchForValue']);

            $html .= '<tr><td colspan="' . self::getDatagrid()->getColumnCount() . '">';
            $html .= _('Search: ');
            $html .= '<input type="text"';
            $html .= ' value="' . $value . '"';
            $html .= ' name="' . self::getDatagrid()->getParameterAlias('SearchForValue') . '" />';
            $html .= ' <select name="' . self::getDatagrid()->getParameterAlias('SearchColumn') . '">';

            $columnsArray = self::getDatagrid()->getColumns();

            foreach ($columnsArray as $columnObject) {

                if ($columnObject->isEnabled('Search')) {
                    $searchColumn = $_SESSION['Datagrid_' . self::getDatagrid()->getAlias()]['SearchColumn'];
                    $selected = ($searchColumn == $columnObject->getAlias()) ? ' selected="selected"' : '';
                }

                $html .= '<option value="' . $columnObject->getAlias() . '"' . $selected . '>';
                $html .= $columnObject->getName() . '</option>';
            }
        }

        $html .= '</select>';
        $html .= ' <input type="submit" class="ButtonGreen" value="' . _('Search') . '" />';
        $html .= '</td></tr>';

        return $html;
    }

    /**
     * Renders the datagrid table
     *
     * @return string Returns the HTML representation of the datagrid table
     */
    public static function render()
    {
        // Build htmlcode
        $html = '';

        $html .= '<link rel="stylesheet" type="text/css"';
        $html .= ' href="' . WWW_ROOT_THEMES_CORE . 'css/pagination.css" />' . CR;
        $html .= '<link rel="stylesheet" type="text/css"';
        $html .= ' href="' . WWW_ROOT_THEMES_CORE . 'css/datagrid.css" />' . CR;
        $html .= '<script src="' . WWW_ROOT_THEMES_CORE . 'javascript/datagrid.js"';
        $html .= ' type="text/javascript"></script>' . CR;

        $html .= '<form action="' . self::getDatagrid()->getBaseURL() . '" method="post"';
        $html .= ' name="Datagrid-' . self::getDatagrid()->getAlias() . '"';
        $html .= ' id="Datagrid-' . self::getDatagrid()->getAlias() . '">' . CRT;

        /**
         * Add hidden input fields to store the parameters of the datagrid between requests.
         */
        $input_field_sprintf = '<input type="hidden" name="%s" value="%s" />';

        // hidden inputfield PAGE
        $html .= sprintf(
            $input_field_sprintf,
            self::getDatagrid()->getParameterAlias('Page'),
            self::getDatagrid()->getCurrentPage()
        );

        // hidden inputfield ResultsPerPage
        $html .= sprintf(
            $input_field_sprintf,
            self::getDatagrid()->getParameterAlias('ResultsPerPage'),
            self::getDatagrid()->getResultsPerPage()
        );

        // hidden inputfield SortColumn
        $html .= sprintf(
            $input_field_sprintf,
            self::getDatagrid()->getParameterAlias('SortColumn'),
            self::getDatagrid()->getSortColumn()
        );

        // hidden inputfield SortOrder
        $html .= sprintf(
            $input_field_sprintf,
            self::getDatagrid()->getParameterAlias('SortOrder'),
            self::getDatagrid()->getSortOrder()
        );

        $html .= '<div class="Datagrid ' . self::getDatagrid()->getClass() . '">' . CR;

        /**
         * Main Method - This will render the table.
         */
        $html .= self::renderTable();

        $html .= '</div>' . CR;

        $html .= '</form>' . CR;

        return $html;
    }

    public function __toString()
    {
        return self::render();
    }
}
