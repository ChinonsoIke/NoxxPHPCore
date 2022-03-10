<?php

namespace App\Core\Forms;

use App\Core\Model;

abstract class BaseField
{
    public $model;
    public $attribute;
    
    public function __construct(Model $model, $attribute)
    {
        $this->model= $model;
        $this->attribute= $attribute;
    }

    abstract public function renderInput() : string;

    public function __toString()
    {
        return sprintf(
            '<div class="mb-3">
                <label for="%s" class="form-label">%s</label>
                %s
                <div class="invalid-feedback">%s</div>
            </div>',
            $this->attribute,
            $this->attribute,
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}