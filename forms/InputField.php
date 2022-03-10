<?php

namespace App\Core\Forms;

use App\Core\Model;

class InputField extends BaseField
{
    public const TYPE_TEXT= 'text';
    public const TYPE_PASSWORD= 'password';
    public const TYPE_EMAIL= 'email';
    public const TYPE_NUMBER= 'number';

    public $type= self::TYPE_TEXT;

    public function __construct(Model $model, $attribute)
    {
        parent::__construct($model, $attribute);
    }

    public function passwordType()
    {
        $this->type= self::TYPE_PASSWORD;
        return $this;
    }

    public function emailType()
    {
        $this->type= self::TYPE_EMAIL;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('
        <input type="%s" name="%s" value="%s" class="form-control%s">',
        $this->type,
        $this->attribute,
        $this->model->{$this->attribute},
        $this->model->hasError($this->attribute) ? ' is-invalid':'',
        );
    }
}