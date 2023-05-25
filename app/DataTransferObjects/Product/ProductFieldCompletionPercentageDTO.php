<?php

namespace App\DataTransferObjects\Product;

use App\DataTransferObjects\DTO;

class ProductFieldCompletionPercentageDTO extends DTO
{
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $color
     */
    public $color;
    /**
     * @var string $backgroundColor
     */
    public $backgroundColor;
    /**
     * @var int $percent
     */
    public $percent;

    public function __construct(string $name, string $color, string $backgroundColor, int $percent)
    {
        $this->name = $name;
        $this->color = $color;
        $this->backgroundColor = $backgroundColor;
        $this->percent = $percent;
    }
}
