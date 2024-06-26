<?php
/**
 * Smarty plugin.
 */

/**
 * Name:         gravatar
 * Type:         function
 * Purpose: This TAG inserts a valid Gravatar Image.
 *
 * See http://en.gravatar.com/ for further information.
 *
 * Parameters:
 * - email      = the email to fetch the gravatar for (required)
 * - size       = the images width
 * - rating     = the highest possible rating displayed image [ G | PG | R | X ]
 * - default    = full url to the default image in case of none existing OR
 *                invalid rating (required, only if "email" is not set)
 *
 * Example usage:
 *
 * {gravatar email="example@example.com" size="40" rating="R" default="http://myhost.com/myavatar.png"}
 *
 * @param array $params as described above (emmail, size, rating, defaultimage)
 *
 * @return null|Koch\View\Helper\Gravatar
 */
function Smarty_function_gravatar($params)
{
    $email = $defaultImage = $size = $rating = '';

    // check for email adress
    if (isset($params['email'])) {
        $email = trim(mb_strtolower((string) $params['email']));
    } else {
        trigger_error("Gravatar Image couldn't be loaded! Parameter 'email' not specified!");

        return;
    }

    // default avatar
    if (isset($params['default'])) {
        $defaultImage = urlencode((string) $params['default']);
    }

    // size
    if (isset($params['size'])) {
        $size = $params['size'];
    }

    // rating
    if (isset($params['rating'])) {
        $rating = $params['rating'];
    }

    return new \Koch\View\Helper\Gravatar($email, $rating, $size, $defaultImage);
}
