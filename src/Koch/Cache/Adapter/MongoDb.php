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

namespace Koch\Cache\Adapter;

use Koch\Cache\AbstractCache;
use Koch\Cache\CacheInterface;

/**
 * Cache Handler via MongoDb.
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

    public function __construct($options = [])
    {
        if (class_exists('Mongo') === false) {
            throw new \Koch\Exception\Exception('MongoDb was not found.');
        }

        // omg. let's grab the passed in mock objects from the $options array
        if (UNIT_TEST_RUN === true) {
            $this->mongo    = $options['mock']['mongo'];
            $this->database = $options['mock']['mongodb'];
            unset($options['mock']);
        }

        $defaultOptions = [
            'database'   => 'kf_database',
            'collection' => 'kf_coll',
        ];
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

        if (empty($this->options['database'])) {
            throw new \RuntimeException('The option "database" must be set.');
        }
        if (empty($this->options['collection'])) {
            throw new \RuntimeException('The option "collection" must be set.');
        }

        $this->database   = $this->mongo->selectDB($this->options['database']);
        $this->collection = $this->database->selectCollection($this->options['collection']);
    }

    public function clear()
    {
        return $this->collection->drop();
    }

    public function fetch($key)
    {
        $data = $this->getData($key);

        return $data ?? false;
    }

    /**
     * @param string $value
     */
    public function store($key, $value, $ttl = null)
    {
        $item = [
            'key'   => $key,
            'value' => $value,
            'ttl'   => time() + $ttl,
        ];
        $this->delete($key);

        return $this->collection->insert($item);
    }

    public function contains($key)
    {
        return ($this->getData($key)) ? true : false;
    }

    public function delete($key)
    {
        return $this->collection->remove(['key' => $key]);
    }

    /**
     * Get data value from mongo database and performs a TTL check.
     *
     * @param string $key Keyname
     *
     * @return mixed|false
     */
    public function getData($key)
    {
        $data = $this->collection->findOne(['key' => $key]);

        if (count($data) > 1) {
            if (time() > $data['ttl']) {
                return false;
            }

            return $data['value'];
        }

        return false;
    }

    /**
     * @todo
     *
     * @return array
     */
    public function stats()
    {
        return [];
    }
}
