<?php

namespace App\Helpers\WikipediaCaptcha;


class Question
{
    public $text;
    public $hiddenValue;

    public function __construct(string $text, string $hiddenValue)
    {
        $this->text = $text;
        $this->hiddenValue = $hiddenValue;
    }
}