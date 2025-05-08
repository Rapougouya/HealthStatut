<?php

namespace App\View\Components\Patient;

use Illuminate\View\Component;

class Detail extends Component
{
    public $label;
    public $value;

    public function __construct($label, $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.patient.detail');
    }
}
