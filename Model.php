<?php

namespace App\Core;

abstract class Model
{
    public const RULE_REQUIRED= 'required';
    public const RULE_EMAIL= 'email';
    public const RULE_MIN= 'min';
    public const RULE_MAX= 'max';
    public const RULE_MATCH= 'match';
    public const RULE_UNIQUE= 'unique';

    public array $errors= [];

    // store values of input data in class properties
    public function loadData($data)
    {
        foreach($data as $key=>$value){
            if(property_exists($this, $key)){
                $this->{$key}= $value;
            }
        }
    }

    abstract public function rules();

    /**
     * Validate user input and make sure it is according to set rules
     */
    public function validate()
    {
        foreach($this->rules() as $attribute=>$rules){
            $value= $this->{$attribute};
            foreach($rules as $rule){
                $ruleName= $rule;
                if(is_array($ruleName)){
                    $ruleName= $rule[0];
                }

                if($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addErrorWithRule($attribute, $ruleName);
                }
                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addErrorWithRule($attribute, $ruleName);
                }
                if($ruleName === self::RULE_MIN && strlen($value)<$rule['min']){
                    $this->addErrorWithRule($attribute, $ruleName, $rule['min']);
                }
                if($ruleName === self::RULE_MAX && strlen($value)>$rule['max']){
                    $this->addErrorWithRule($attribute, $ruleName, $rule['max']);
                }
                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['matchTo']}){
                    $this->addErrorWithRule($attribute, $ruleName);
                }
                if($ruleName === self::RULE_UNIQUE){
                    $className= $rule['class'];
                    $uniqueAttr= $rule['attribute'] ?? $attribute;
                    $tableName= $className::tableName();
                    $statement= Application::$app->db->prepare("SELECT * FROM $tableName WHERE
                        $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();

                    $record= $statement->fetchObject();
                    if($record){
                        $this->addErrorWithRule($attribute, $ruleName, $attribute);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * add error to errors array
     */
    private function addErrorWithRule($attribute, $ruleName, $param=null)
    {
        $message= $this->errorMessages()[$ruleName] ?? '';
        $message= str_replace('{param}', $param, $message);
        $this->errors[$attribute][]= $message;
    }

    public function addError($attribute, $message)
    {
        $this->errors[$attribute][]= $message;
        // echo '<pre>';var_dump($this->errors);echo '</pre>';exit;
    }

    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED=> 'This field is required',
            self::RULE_EMAIL=> 'This field must be a valid email address',
            self::RULE_MIN=> 'This field must contain a minimum of {param} characters',
            self::RULE_MAX=> 'This field must contain a maximum of {param} characters',
            self::RULE_MATCH=> 'Passwords do not match',
            self::RULE_UNIQUE=> 'This {param} already belongs to an existing user',
        ];
    }

    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}