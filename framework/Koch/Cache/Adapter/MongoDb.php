<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;

/**
 * Koch Framework - Cache Handler via MongoDb.
 *
 * MongoDB (from "humongous") is a scalable, high-performance, open source NoSQL database
 * developed by 10gen. MongoDB stores its documents in a binary-encoded format called BSON.
 * BSON extends the JSON data model to provide additional types and to be efficient for
 * encoding and decoding within different languages.
 *
 * @link http://www.mongodb.org/
 * @link http://www.10gen.com/
 * @link http://php.net/manual/de/class.mongodb.php
 * @link http://php.net/manual/de/class.mongocollection.php
 */
class MongoDb extends AbstractCache implements CacheInterface
{
    /**
     * @var object MongoDb instance
     */
    public $mongo;

    /* @var database connection */
    public $database;

    /* @var \MongoCollection */
    public $collection;

    public function __construct($options = array())
    {

        if (class_exists('Mongo') === false) {
            throw new \Koch\Exception\Exception('The MongoDb was not found.');
        }

        // omg. let's grab the passed in mock objects from the $options array
        if (UNIT_TEST_RUN == true) {
            $this->mongo = $options['mock']['mongo'];
            $this->database = $options['mock']['mongodb'];
            unset($options['mock']);
        }

        $defaultOptions = array(
            'database' => 'kf_database',
            'collection' => 'kf_coll'
        );
        $options = $options + $defaultOptions;

        parent::__construct($options);

        if (!$this->mongo) {
            // "mongodb://admin:passwd@remotemongoserver:27017"
            $this->mongo = new \MongoDB(); // connects to localhost:27017 by default
        }

        if ($this->mongo === false) {
            throw new \MongoConnectionException('Connecting to MongoDb failed. Check server, port and credentials.');
        }

        $this->initialize();
    }

    public function setOption($key, $value)
    {
        switch ($key) {
            case 'database':
                 $database = (string) $value;
                if ($database === '') {
                    throw new \InvalidArgumentException('Database can not be empty.');
                }
                $this->options['database'] = $database;
                break;
             case 'collection':
                 $database = (string) $value;
                if ($database === '') {
                    throw new \InvalidArgumentException('Collection can not be empty.');
                }
                $this->options['collection'] = $database;
                break;
            default:
                // maybe the option is known on the parent class, otherwise will throw excetion
                parent::setOption($key, $value);
        }

        return true;
    }

    /**
     * Select Database and initialize the MongoDb collection.
     *
     * @throws \RuntimeException
     */
    public function initialize()
    {
        if (null !== $this->collection) {
            return;
        }

        if (empty($this->options['database']) === true) {
            throw new \RuntimeException('The option "database" must be set.');
        }
        if (empty($this->options['collection']) === true) {
            throw new \RuntimeException('The option "collection" must be set.');
        }

        $this->database = $this->mongo->selectDB($this->options['database']);
        $this->collection = $this->database->selectCollection($this->options['collection']);
    }

    public function clear()
    {
        return $this->collection->drop();
    }

    public function fetch($key)
    {
        $data = $this->getData($key);

        return (isset($data) === true) ? $data : false;
    }

    public function store($key, $value, $ttl = null)
    {
        $item = array(
            'key'   => $key,
            'value' => $value,
            'ttl'   => time() + $ttl,
        );
        $this->delete($key);

        return $this->collection->insert($item);
    }

    public function contains($key)
    {
        return ($this->getData($key)) ? true : false;
    }

    public function delete($key)
    {
        return $this->collection->remove(array('key' => $key));
    }

    /**
     * Get data value from mongo database and performs a TTL check.
     *
     * @param  string      $key Keyname
     * @return mixed|false
     */
    public function getData($key)
    {
        $data = $this->collection->findOne(array('key' => $key));

        if (count($data) > 1) {
            if (time() > $data['ttl']) {
                return false;
            }

            return $data['value'];
        }

        return false;
    }

    public function stats()
    {
        // @todo
        return array();
    }
}
