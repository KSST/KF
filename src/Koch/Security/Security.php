<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\Security;

/**
 * Class for Security Handling.
 *
 * The class contains helper functions for hashing and salting strings (e.g. passwords).
 * Password hashing is not "password encryption".
 * An encryption is reversible. A hash is not reversible.
 * A salted hash is a combination of a string and a random value.
 * Both (salt and hash) are stored in the database for each user individually.
 *
 * @link http://www.schneier.com/cryptography.html Website of Bruce Schneier
 * @link http://www.php.net/manual/en/refs.crypto.php
 */
final class Security
{
    /**
     * Checks whether a hashed password matches a stored salted+hashed password.
     *
     * @param string $passwordhash   The incomming hashed password. There is never a plain-text password incomming.
     * @param string $databasehash   The stored password. It's a salted hash.
     * @param string $salt           The salt from db.
     * @param string $hash_algorithm The hashing algorithm to use.
     *
     * @return bool true if the incomming hashed password matches the hashed+salted in db,
     *              false otherwise
     */
    public static function checkSaltedHash($passwordhash, $databasehash, $salt, $hash_algorithm)
    {
        // combine incomming $salt and $passwordhash (which is already sha1)
        $salted_string = $salt . $passwordhash;

        // get hash_algo from config and generate hash from $salted_string
        $hash = self::generateHash($hash_algorithm, $salted_string);

        // then compare
        return $databasehash === $hash;
    }

    /**
     * This functions takes a clear (password) string and prefixes a random string called
     * "salt" to it. The new combined "salt+password" string is then passed to the hashing
     * method to get an hash return value.
     * So whatï¿½s stored in the database is Hash(password, users_salt).
     *
     * Why salting? 2 Reasons:
     * 1) Make Dictionary Attacks (pre-generated lists of hashes) useless
     *    The dictionary has to be recalculated for every account.
     * 2) Using a salt fixes the issue of multiple user-accounts having the same password
     *    revealing themselves by identical hashes. So in case two passwords would be the
     *    same, the random salt makes the difference while creating the hash.
     *
     * @param string A clear-text string, like a password "JohnDoe$123"
     * @param string The hash algorithm to use.
     *
     * @return array $hash Array containing ['salt'] and ['hash']
     */
    public static function buildSaltedHash($string = '', $hash_algorithm = '')
    {
        // set up the array
        $salted_hash_array = [];
        // generate the salt with fixed length 6 and place it into the array
        $salted_hash_array['salt'] = self::generateSalt(6);
        // combine salt and string
        $salted_string = $salted_hash_array['salt'] . $string;
        // generate hash from "salt+string" and place it into the array
        $salted_hash_array['hash'] = self::generateHash($hash_algorithm, $salted_string);
        // return array with elements ['salt'], ['hash']
        return $salted_hash_array;
    }

    /**
     * This function generates a HASH of a given string using the requested hash_algorithm.
     * When using hash() we have several hashing algorithms like: md5, sha1, sha256 etc.
     * To get a complete list of available hash encodings use: print_r(hash_algos());
     * When you have the "skein_hash" extension installed, we use "skein_hash".
     * When it's not possible to use hash() or skein_hash() for any reason, we use "md5" and "sha1".
     *
     * @link http://www.php.net/manual/en/ref.hash.php
     *
     * @param $string String to build a HASH from
     * @param $hash_type Encoding to use for the HASH (sha1, md5) default = sha1
     * @param string $hash_algorithm
     *
     * @return string The hashed string.
     */
    public static function generateHash($hash_algorithm = null, $string = '')
    {
        /*
         * check, if we can use skein_hash()
         *
         * therefore the php extension "skein" has to be installed.
         * website: http://www.skein-hash.info/downloads
         */
        if (extension_loaded('skein') && ($hash_algorithm === 'skein')) {
            // get the binary 512-bits hash of string
            return skein_hash($string, 512);
        }

        return hash((string) $hash_algorithm, (string) $string);
    }

    /**
     * Get random string/salt of size $length.
     *
     * @param int $length Length of random string to return
     *
     * @return string Returns a string with random generated characters and numbers
     */
    public static function generateSalt($length)
    {
        // set salt to empty
        $salt = '';

        // the primary choice for a cryptographic strong randomness function is
        // openssl_random_pseudo_bytes.
        if (true === function_exists('openssl_random_pseudo_bytes')) {
            // generate a pseudo-random string of bytes
            $bytes = openssl_random_pseudo_bytes($length);

            // bytes to hexadecimal to decimal
            $string = hexdec(bin2hex($bytes));

            // truncate the string to correct length
            $salt = substr($string, 0, $length);

            return $salt;
        }

        /*
         * If "ext/mcrypt" is available, then we gather entropy from the
         * operating system's PRNG. This is better than reading /dev/urandom
         * directly since it avoids reading larger blocks of data than needed.
         */
        if (true === function_exists('mcrypt_create_iv')) {
            // generate a pseudo-random string of bytes
            $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            // bytes to hexadecimal to decimal
            $string = hexdec(bin2hex($bytes));

            // truncate the string to correct length
            $salt = substr($string, 0, $length);

            return $salt;
        }

        /*
         * use mt_srand
         *
         * mt_srand() and mt_rand() are used to generate even better randoms,
         * because of mersenne-twisting. still worse, but better then rand().
         *
         * Security Note! This is considered a week seeding. As of PHP 5.3
         * openssl_random_pseudo_bytes() from "ext/openssl" is primary choice.
         */

        // seed the randoms generator with microseconds since last "whole" second
        mt_srand((double) microtime() * 1000000);
        // set up the random chars to choose from
        $chars = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        // count the number of random_chars
        $number_of_random_chars = strlen($chars) - 1;
        // add a char from the random_chars to the salt, until we got the wanted $length
        while (strlen($salt) < $length) {
            // get a random char of $chars
            $char_to_add = $chars[mt_rand(0, $number_of_random_chars)];
            // ensure that a random_char is not used twice in the salt
            if (!str_contains($salt, $char_to_add)) {
                // finally => add char to salt
                $salt .= $char_to_add;
            }
        }

        return $salt;
    }
}
