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

namespace) is used for all actions.
     * Example: A action named "show" will be mapped to "actionShow()"
     * This is also a way to ensure some kind of whitelisting via namespacing.
     *
     * The convention is action<action> !
     *
     *
     * @param  string $action the action
     * @return string the mapped method name
     */
    public static function mapActionToMethodname($action = null)
    {
        // set default value for action, when not set by URL
        if ($action === null) {
            $action = self::DEFAULT_ACTION;
        }

        // all application actions are prefixed with 'action'
        // e.g. action_<login>
        return self::ACTION_PREFIX . ucfirst($action);
    }
}
