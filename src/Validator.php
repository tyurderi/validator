<?php

namespace Validator;

class Validator implements ValidatorInterface
{

    protected static $__rules = array();

    protected $fields   = array();

    protected $errors   = array();

    protected $rules    = array();

    protected $canceled = false;

    public function __construct()
    {
        require_once __DIR__ . '/Rules.php';
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $value, $rules, $messages = array())
    {
        $this->fields[$name] = array(
            'value'    => $value,
            'rules'    => $rules,
            'messages' => $messages
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if ($this->has($name))
        {
            unset($this->fields[$name]);
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addRule($name, $callable)
    {
        if (!is_callable($callable))
        {
            trigger_error('Invalid value for parameter \$callable.', E_USER_ERROR);
        }

        $this->rules[$name] = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public static function addGlobalRule($name, $callable)
    {
        if (!is_callable($callable))
        {
            trigger_error('Invalid value for parameter \$callable.', E_USER_ERROR);
        }
        
        self::$__rules[$name] = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function passes()
    {
        return empty($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        foreach ($this->fields as $fieldName => $fieldData)
        {
            $fieldValue    = $fieldData['value'];
            $fieldRules    = $this->parseRules($fieldData['rules']);
            $fieldMessages = $fieldData['messages'];

            foreach ($fieldRules as $ruleName => $ruleParams)
            {
                if (!isset($fieldMessages[$ruleName]) || empty($fieldMessages[$ruleName]))
                {
                    trigger_error(
                        sprintf('Error message for rule %s for field %s is empty.', $ruleName, $fieldName),
                        E_USER_NOTICE
                    );
                }

                $rulePasses = $this->executeRule($ruleName, $fieldValue, $ruleParams);

                if (!$rulePasses)
                {
                    $ruleMessage    = isset($fieldMessages[$ruleName]) ? $fieldMessages[$ruleName] : '';
                    $this->errors[] = $ruleMessage;
                }

                if ($this->canceled)
                {
                    break 2;
                }
            }
        }

        return $this;
    }

    protected function executeRule($ruleName, $fieldValue, $ruleParams)
    {
        $methodParams = array($this->fields, $fieldValue, $ruleParams, $this->canceled);
        $method       = $this->getRuleMethod($ruleName);

        return call_user_func_array($method, $methodParams);
    }

    protected function getRuleMethod($ruleName)
    {
        $methodName = 'validator_rule_' . $ruleName;

        if (function_exists($methodName))
        {
            return $methodName;
        }
        else if(isset($this->rules[$ruleName]))
        {
            return $this->rules[$ruleName];
        }
        else if(isset(self::$__rules[$ruleName]))
        {
            return self::$__rules[$ruleName];
        }

        return trigger_error(sprintf('Undefined rule: %s.', $ruleName), E_USER_ERROR);
    }

    /**
     * An example rule string could look like the following:
     * required|min_length:3|between:3,4
     *
     * @param string $ruleString
     *
     * @return array
     */
    protected function parseRules($ruleString)
    {
        $ruleItems = explode('|', $ruleString);
        $rules     = array();

        foreach ($ruleItems as $ruleItem)
        {
            $name   = $ruleItem;
            $params = array();

            if ($pos = strpos($ruleItem, ':'))
            {
                $name   = substr($ruleItem, 0, $pos);
                $params = explode(',', substr($ruleItem, $pos + 1));
            }

            $rules[$name] = $params;
        }

        return $rules;
    }

}