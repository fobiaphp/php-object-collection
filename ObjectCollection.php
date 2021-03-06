<?php
/**
 * PHP Object Collection
 *
 * @author      Dmitriy Tyurin <fobia3d@gmail.com>
 * @copyright   Copyright (c) 2015 Dmitriy Tyurin
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
 * Колекция объектов. Позволяет работать сразу над всеми объектами, фильтравать, устанавливать и извлекать их свойства.
 *
 * Все элементы преобразуются в объекты.
 * По умолчанию список может содержать повторяющиеся объекты, а преобразованые
 * элементы являються каждый уникальным.
 *
 *
 * @package Fobia
 * @author  Dmitriy Tyurin <fobia3d@gmail.com>
 */
class ObjectCollection implements \IteratorAggregate, \Countable
{

    /**
     * Список объектов
     *
     * @var array
     */
    protected $data = array();

    /**
     * Количество объектов
     *
     * @var int
     */
    private $_count = 0;

    /**
     * Флак, что все объекты уникальные
     *
     * @var boolean
     */
    private $_unique;

    /**
     * Конструктор.
     *
     * @param array $data
     * @param bool  $unique
     */
    public function __construct($data = array(), $unique = false)
    {
        $this->_unique = $unique;

        if ($data) {
            $this->merge($data);
        }
        $this->_resor(false);
    }

    /**
     * Выбрать непосредственно экземпляр объекта по его индексу.
     *
     * @param int $index индекс объекта
     *
     * @return \stdClass
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
     * <code>
     * // Поиск объектов с существующим свойством
     * $oc->find('Location');
     *
     * // Поиск объектов со свойством равным указаному значению
     * $oc->find('Location', 'localhost/js');
     *
     * // Поиск объектов удавлетворяющие возврату функции
     * $oc->find(function($obj, $key));
     * </code>
     *
     * @param string|callable $name    название свойства или функция обратного вызова.
     *                                 в функцию передаеться [оъект, его индекс]
     * @param mixed           $value   его значение, если $name строка
     *
     * @return \Fobia\ObjectCollection  колекция найденных объектов.
     */
    public function find($name, $value = null)
    {
        $data = array();

        // Функция пользователя
        if (!is_string($name) && is_callable($name)) {
            $callback = $name;
            foreach ($this->data as $key => $obj) {
                if ($callback($obj, $key)) {
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

        // Сравнение свойства
        if (func_num_args() > 1) {
            foreach ($this->data as $obj) {
                if ($obj->$name == $value) {
                    $data[] = $obj;
                }
            }
        }

        return new self($data, $this->_unique);
    }

    /**
     * Отфильтровать список объектов используя функции обратного вызова.
     *
     * В Функцию передаються объект и его индекс.
     * Все объекты на которые функция вернула false, исключаються.
     *
     * @param callable $callback
     *
     * @throws \InvalidArgumentException
     * @return self
     */
    public function filter($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("CORE: Параметр не является функцией обратного вызова.");
        }

        $arr = array();
        foreach ($this->data as $key => $obj) {
            // if (call_user_func_array($callback, array($obj, $key))){
            if ($callback($obj, $key)) {
                $arr[] = $obj;
            }
        }

        $this->data = $arr;
        $this->_resor(false);

        return $this;
    }

    /**
     * Возвращает индекс объекта в колекции
     *
     * @param mixed   $object
     * @param boolean $strict точное совпадение объекта
     *
     * @return array
     */
    public function index($object, $strict = true)
    {
        $keys = array_keys($this->data, $object, $strict);
        return $keys;
    }

    /**
     * Установить свойства в новое значение.
     *
     * @param string $name  имя свойства
     * @param mixed  $value устанавливаемое значение
     *
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
     * Если второй аргумент не передан, то возвращаеться список значений свойств
     * $name либо список масивов со свойствами.
     * Если передан второй параметр возвращаеться ассоциативный массив, сформированый
     * аналогично первому варианту, а ключи являються значения имени свойства по первому
     * аргументу. (Дубликаты замещаються)
     *
     * <code>
     * // Вернет список определеного поля
     * $oc->get('login');
     *
     * // Вернет список масивов полей
     * $oc->get(array('login', 'password'))
     *
     * // Вернет ассоциативный массив, где ключом являеться первый аргумент,
     * // а значения поля объектов, сформированых из второго аргумента
     * $oc->get('key', 'login');
     * $oc->get('key', array('login', 'password');
     * </code>
     *
     * @param string|array $name
     * @param string|array $fields
     *
     * @return array
     */
    public function get($name, $fields = null)
    {
        $data = array();

        // список масивов полей
        if (is_array($name)) {
            foreach ($this->data as $obj) {
                $item = array();
                foreach ($name as $key) {
                    $item[$key] = $obj->$key;
                }
                $data[] = $item;
            }
            return $data;
        }

        // список определеного поля
        if ($fields === null) {
            foreach ($this->data as $obj) {
                $data[] = $obj->$name;
            }
            return $data;
        }

        // ассоциативный массив полей по ключу
        if (is_array($fields)) {
            foreach ($this->data as $obj) {
                $item = array();
                foreach ($fields as $key) {
                    $item[$key] = $obj->$key;
                }
                $data[$obj->$name] = $item;
            }
            return $data;
        } else {
            // ассоциативный массив полея
            foreach ($this->data as $obj) {
                $data[$obj->$name] = $obj->$fields;
            }
            return $data;
        }
    }


    public function geteach($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Параметр не являеться функцией");
        }
        $result = array();
        foreach ($this->data as $k => $value) {
            $result[$k] = call_user_func_array($callback, array($value, $k));
        }

        return $result;
    }

    /**
     * Вызвать метод во всех объектах колекции
     *
     * @param string $name
     * @param array  $args
     *
     * @return array
     */
    public function call($name, array $args = array())
    {
        $arr = array();
        foreach ($this->data as $item) {
            if (method_exists($item, $name)) {
                $arr[] = call_user_func_array(array($item, $name), $args);
            }
        }

        return $arr;
    }

    /**
     * Добавить объект в коллекцию.
     *
     * @param \stdClass $object
     *
     * @return $this
     */
    public function add($object)
    {
        $strict = true;
        if (!is_object($object)) {
            $object = (object)$object;
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

        array_push($this->data, $object);
        $this->_resor(true);

        return $this;
    }

    /**
     * Добавить объект в коллекцию.
     *
     * @param \stdClass $object позиция
     * @param int       $index  позиция
     *
     * @return self
     */
    public function addAt($object, $index = null)
    {
        $strict = true;
        if (!is_object($object)) {
            $object = (object)$object;
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
            $arr_after = array_slice($this->data, $index);

            $this->data = array_merge($arr_before, array($object), $arr_after);
        }

        $this->_resor(true);

        return $this;
    }

    /**
     * Сливает масив объектов в текущюю колекцию
     *
     * @param array|ObjectCollection $data
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function merge($data)
    {
        if ($data instanceof ObjectCollection) {
            $data = $data->toArray();
        } elseif ($data instanceof \Traversable) {
            $_data = array();
            foreach ($data as $value) {
                $_data[] = (object)$value;
            }
            $data = $_data;
            unset($_data);
        } elseif (is_array($data)) {
            array_walk($data, function (&$value) {
                $value = (object)$value;
            });
        } else {
            throw new \InvalidArgumentException("Параметр не являеться масивом");
        }

        $this->data = array_merge($this->data, $data);

        if ($this->_unique) {
            $this->unique();
        }

        $this->_resor(true);

        return $this;
    }

    /**
     * Очистить
     *
     * @return $this
     */
    public function clear()
    {
        $this->data = array();
        $this->_count = 0;

        return $this;
    }

    /**
     * Перезаписать список в другую колекцию
     *
     * @param \Fobia\ObjectCollection $collection
     *
     * @return $this
     */
    public function replaceTo(ObjectCollection &$collection)
    {
        // $collection->clear()->merge($this);
        $collection = $this;
        return $this;
    }

    /**
     * Удалить объект с указаной позиции из колеции.
     *
     * @param  int $index позиция
     *
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
     * @param mixed|array $objects
     *
     * @return self
     */
    public function remove($objects)
    {
        if (!$objects instanceof \Traversable && !is_array($objects)) {
            $objects = array($objects);
        }

        foreach ($objects as $object) {
            $keys = array_keys($this->data, $object, true);
            foreach ($keys as $key) {
                unset($this->data[$key]);
            }
        }
        $this->_resor(true);
        return $this;
    }

    /**
     * Обходит весь масив, передавая функции объект, его индекс и дополнительные параметры.
     *
     * В функцию передаються [объект, его индекс, дополнительный параметр]
     * Если функция возвращает false, обход останавливаеться.
     *
     * @param callback $callback
     * @param mixed    $args
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function each($callback, $args = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("CORE: Параметр не является функцией обратного вызова.");
        }

        foreach ($this->data as $key => $obj) {
            if ($callback($obj, $key, $args) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Извлекает последний элемент массива
     *
     * @return mixed
     */
    public function pop()
    {
        $val = array_pop($this->data);
        $this->_resor(true);
        return $val;
    }

    /**
     * Извлекает первый элемент массива
     *
     * @return mixed
     */
    public function shift()
    {
        $val = array_shift($this->data);
        $this->_resor(true);
        return $val;
    }

    /**
     * Устанавливает только уникальные элементы
     *
     * @param bool $strict строгое равенство для объектов
     *
     * @return self
     */
    public function unique($strict = true)
    {
        $arr = array();
        foreach ($this->data as $obj) {
            if (!array_keys($arr, $obj, $strict)) {
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
     * @param callback $callable int callback ( mixed $a, mixed $b )
     * @param mixed    $args
     *
     * @return self
     * @throws \InvalidArgumentException
     */
    public function sort($callable, $args = null)
    {
        if (is_callable($callable)) {
            usort($this->data, $this->_sort_callable($callable, $args));
        } else {
            throw new \InvalidArgumentException("Плохой параметр сортировки");
        }

        return $this;
    }

    /**
     *
     * @param callable $callable
     * @param mixed    $args
     *
     * @return callable
     *
     * @codeCoverageIgnore
     */
    protected function _sort_callable($callable, $args = null)
    {
        return function ($a, $b) use ($callable, $args) {
            return $callable($a, $b, $args);
        };
    }

    /**
     * Пересобрать список объектов
     *
     * @param boolean $resor_keys перенумеровать ключи
     *
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
     * Return getIterator
     *
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
