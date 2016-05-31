<?php

namespace Validator;

interface ValidatorInterface
{

    /**
     * @param string $name
     * @param string $value
     * @param string $rules
     * @param array  $messages
     *
     * @return ValidatorInterface
     */
    public function add($name, $value, $rules, $messages = array());

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function has($name);

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function remove($name);

    /**
     * @return ValidatorInterface
     */
    public function validate();

    /**
     * @return boolean
     */
    public function passes();

    /**
     * @param string $name
     * @param mixed  $callable
     *
     * @return ValidatorInterface
     */
    public function addRule($name, $callable);

    /**
     * @param string $name
     * @param mixed  $callable
     *
     * @return ValidatorInterface
     */
    public static function addGlobalRule($name, $callable);

    /**
     * @return array
     */
    public function errors();

}