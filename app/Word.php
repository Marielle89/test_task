<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $fillable = ['word', 'list_id'];

    public function scopeListId($query, $list_id)
    {
        return $query
            ->where('list_id', '=', $list_id);
    }
}
