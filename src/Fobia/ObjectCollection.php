<?php
/**
 * PHP Object Collection
 *
 * @author      Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright   Copyright (c) 2014 Dmitriy Tyurin
 * @package     Fobia
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Fobia;

/**
 * ObjectCollection
 *
 * @package  Fobia
 * @author   Dmitriy Tyurin
 */
class ObjectCollection implements \IteratorAggregate, \Countable
{

    /** @var array */
    protected $data = array();

    /** @var int */
    private $_count = 0;

    /** @var boolean */
    private $_unique = false;

    /**
     * @internal
     */
    public function __construct(array $data = array())
    {
        if (count($data)) {
            $this->merge($data);
        }
        $this->_resor(false);
    }

    /**
     * Выбрать непосредственно экземпляр объекта.
     *
     * @param int $index индекс объекта
     * @return \stdObject
     */
    public function eq($index = 0)
    {
        return $this->data[$index];
    }

    /**
     * Найти все элементы, параметр которых удовлетворяют услови.
     *
     * Возвращает новый объект
     *
     * ### Поиск объектов с существующим свойством
     * e.g `find('Location');`
     *
     * ### Поиск объектов со свойством равным указаному значению
     * e.g `find('Location', 'localhost/js');`
     *
     * ### Поиск объектов удавлетворяющие возврату функции
     * e.g `find('Location', function($name_value, $obj, $args), $args);`
     *
     * ### Поиск объектов удавлетворяющие возврату функции
     * e.g `find(function($obj, $args...),  $args, ...);`
     *
     * @param string   $name       название свойства
     * @param mixed    $param      его значение или функция обратного вызова.
     *                             в функцию передаеться [значение поля, оъект, $args]
     * @param mixed    $args       дополнительные параметры, переданные в функцию
     *                             обратного вызова.
     * @return \Fobia\ObjectCollection  колекция найденных объектов.
     *
     * @api
     */
    public function find($name, $param = null, $args = null)
    {
        $data = array();

        if (!is_string($name) && is_callable($name)) {
            $args = func_get_args();
            array_shift($args);

            foreach ($this->data as $obj) {
                if (call_user_func_array($name, array_merge(array($obj), $args) ) ){
                    $data[] = $obj;
                }
            }
            return new self($data);
        }

        // Существавание свойства
        if (func_num_args() == 1) {
            foreach ($this->data as $obj) {
                if (isset($obj->$name)) {
                    $data[] = $obj;
                }
            }
        }

        if (func_num_args() > 1) {
            //$args = func_get_args();
            //$args = $args[2];
            // Сравнение свойства
            foreach ($this->data as $obj) {
                // Функция обратного вызова
                if (is_callable($param)) {
                    if ($param($obj->$name, $obj, $args)) {
                        $data[] = $obj;
                    }
                } else {
                    if ($obj->$name == $param) {
                        $data[] = $obj;
                    }
                }
            }
        }

        return new self($data);
    }

    /**
     * Отфильтровать список объектов используя функции обратного вызова.
     * В Функцию передаються объект  и его индекс.
     * Все объекты на которые функция вернула false, исключаються
     *
     * @param callable $callback
     * @param mixed ...
     * @return self
     */
    public function filter($callback)
    {
        if ( ! is_callable($callback)) {
            trigger_error("CORE: Параметр не является функцией обратного вызова.",
                          E_USER_ERROR);
        }

        $args = func_get_args();
        array_shift($args);

        $arr = array();
        foreach ($this->data as $key => $obj) {
            if (call_user_func_array($callback, array_merge(array($obj, $key), $args))){
                $arr[] = $obj;
            }
        }

        $this->data = $arr;
        $this->_resor(false);

        return $this;
    }

    /**
     * Установить свойства в новое значение.
     *
     * @param string   $name    имя свойства
     * @param mixed    $value   устанавливаемое значение
     * @return self
     */
    public function set($name, $value)
    {
        foreach ($this->data as $obj) {
            $obj->$name = $value;
        }

        return $this;
    }

    /**
     * Выбрать значения свойсвта из списка.
     *
     * @param string $name
     * @return array
     */
    public function get($name)
    {
        $data = array();
        foreach ($this->data as $obj) {
            $data[] = $obj->$name;
        }

        return $data;
    }

    /**
     * Добавить объект в коллекцию.
     *
     * @param stdObject   $object    позиция
     * @param int         $index     позиция
     * @return self
     */
    public function addAt($object, $index = null)
    {
        $strict = true;
        if ( ! is_object($object)) {
            $object = (object) $object;
            $strict = false;
        }
        if ($this->_unique) {
            $keys = array_keys($this->data, $object, $strict);
            if ($keys) {
                foreach ($keys as $k) {
                    unset($this->data[$k]);
                }
            }
        }

        if ($index === null || $index >= $this->_count) {
            array_push($this->data, $object);
        } else {
            $arr_before = array_slice($this->data, 0, $index);
            $arr_after =  array_slice($this->data,    $index);

            $this->data = array_merge($arr_before, array($object), $arr_after);
        }
        $this->_resor(true);

        return $this;
    }

    /**
     * Сливает масив объектов в текущюю колекцию
     *
     * @param array|ObjectCollection $data
     * @return self
     */
    public function merge($data)
    {
        if ($data instanceof ObjectCollection) {
            $data = $data->toArray();
        }

        if ( ! is_array($data) ) {
            $data = array($data);
        }

        if (!$this->_unique) {
            array_walk($data, function(&$value) {
                $value = (object) $value;
            });
            $this->data  = array_merge($this->data, $data);
            $this->_resor(false);
        } else {
            foreach ($data as $obj) {
                $this->addAt($obj);
            }
        }

        return $this;
    }

    /**
     * Удалить объект с указаной позиции из колеции.
     *
     * @param  int   $index    позиция
     * @return self
     */
    public function removeAt($index = null)
    {
        if ($index === null) {
            $index = $this->_count - 1;
        }

        unset($this->data[$index]);
        $this->_resor(true);

        return $this;
    }

    /**
     * Удалить объект.
     *
     * @param mixed $object
     * @return self
     */
    public function remove($object)
    {
        $keys = array_keys($this->data, $object, true);
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }
        $this->_resor(true);
        return $this;
    }

    /**
     * Обходит весь масив, передавая функции объект, его индекс и дополнительные параметры.
     * Если функция возвращает false, обход останавливаеться.
     *
     * @param callback $callback
     * @param mixed    $args
     * @return self
     */
    public function each($callback, $args = null)
    {
        if ( ! is_callable($callback)) {
            trigger_error("CORE: Параметр не является функцией обратного вызова.",
                          E_USER_ERROR);
        }

        foreach ($this->data as $key => $obj) {
            if (call_user_func_array($callback, array($obj, $key, $args)) === false) {
            // if ($callback($obj, $key, $args) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Устанавливает только уникальные элементы
     *
     * @return \self
     */
    public function unique($strict = true)
    {
        $arr = array();
        foreach ($this->data as $obj) {
            if ( !array_keys($arr, $obj, $strict) ) {
                $arr[] = $obj;
            }
        }
        $this->data = $arr;
        $this->_resor(false);

        $this->_unique = true;

        return $this;
    }

    /**
     * Сортирует список, используя функцию обратного вызова либо по полю.
     *
     * @param callback|string $param  int callback ( mixed $a, mixed $b )
     * @param mixed           $args
     * @return self
     */
    public function sort($param, $args = null)
    {
        if ( is_string($param) ) {
            usort($this->data, $this->_sort_property($param));
        } else {
            if ( is_callable($param) ) {
                usort($this->data, $param);
            } else {
                usort($this->data, $this->_sort_property($param));
            }
        }

        return $this;
    }

    protected function _sort_callable($callable, $args = null)
    {
        return function($a, $b) use($callable, $args) {
            return $callable($a, $b, $args);
        };
    }

    /**
     * Пересобрать список объектов
     *
     * @param boolean $resor_keys перенумеровать ключи
     * @return int   количество элементов
     */
    protected function _resor($resor_keys)
    {
        if ($resor_keys) {
            $this->data = array_values($this->data);
        }
        $this->_count = count($this->data);
        return $this->_count;
    }

    /**
     * Сортировка по свойству
     *
     * @param string $key
     * @return int
     */
    protected function _sort_property($key = null)
    {
        if ( ! $key ) {
            return 0;
        }
        return function($a, $b) use($key) {
            return strnatcmp($a->$key, $b->$key);
        };
    }
    /**
     * ---------------------------
     *
     * ---------------------------
     */

    /**
     * Количество объектов
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * @internal
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Масив объектов колекции
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}