<?php

namespace App\Imports;

use App\Word;
use Maatwebsite\Excel\Concerns\ToModel;

class WordsImport implements ToModel
{
    protected $list_id;

    public function __construct($list_id)
    {
        $this->list_id = $list_id;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Word([
            'word'     => $row[0],
            'list_id'  => $this->list_id,
        ]);
    }
}
