<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class WordsExport implements FromArray
{
    /**
    * @return \Illuminate\Support\FromArray
    */

    protected $phrases;

    public function __construct(array $phrases)
    {
        $this->phrases = $phrases;
    }

    public function array(): array
    {
        return $this->phrases;
    }
}
