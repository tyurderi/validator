<?php

namespace Validator;

class Validator
{

    protected $fields    = array();

    protected $rules     = array();

    protected $messages  = array();

    protected $errors    = array();

    protected $validated = false;

    public function add($name, $value, $rules, $messages = array())
    {
        $field = new Field();
        $field->name  = $name;
        $field->value = $value;

        foreach(explode('|', $rules) as $rule) {
            $info = explode(',', $rule);

            $name   = $info[0];
            unset($info[0]);
            $params = array_values($info);

            $field->rules[$name] = $params;
        }

        $this->fields[$field->name] = $field;

        if(is_array($messages))
        {
            $this->msg($field->name, $messages);
        }

        return $this;
    }

    /**
     * When callback returns true its marked as passed. When it returns false its marked as no passed and the validator
     * will return false when Validator->passes() is called
     * @param $name
     * @param $callback
     * @return $this
     */
    public function rule($name, $callback)
    {
        $this->rules[$name] = $callback;

        return $this;
    }

    public function msg($field, $input)
    {
        foreach($input as $rule => $msg)
        {
            $this->messages[$field][$rule] = $msg;
        }

        return $this;
    }

    public function validate()
    {
        $this->errors = [];

        $break = false;
        foreach($this->fields as $field)
        {
            /** @var $field Field */

            foreach($field->rules as $rule => $params)
            {
                if(!$this->checkRule($rule, $field->value, $params, $fullBreak))
                {

                    $msg = '#undefined error message on rule: ' . $rule . '#';
                    if(isset($this->messages[$field->name][$rule]))
                    {
                        $msg = $this->messages[$field->name][$rule];
                    }

                    $this->errors[] = $msg;
                    $break = $fullBreak === true;

                    break;
                }
            }

            if($break)
            {
                break;
            }
        }

        $this->validated = true;

        return $this;
    }

    private function checkRule($rule, $value, $params, &$fullBreak)
    {
        if(isset($this->rules[$rule]))
        {
            return $this->rules[$rule]($this->fields, $value, $params, $fullBreak);
        }
        else if(function_exists('validator_rule_' . $rule))
        {
            $function = 'validator_rule_' . $rule;
            return $function($this->fields, $value, $params, $fullBreak);
        }
        else
        {
            throw new \Exception('validator rule \'' . $rule . '\' not found');
        }
    }

    public function passes()
    {
        if($this->validated !== true)
        {
            $this->validate();
        }

        return sizeof($this->errors) === 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }

}