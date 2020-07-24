<?php

namespace Permafrost\Helpers\Tests\Database;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    protected $table = 'examples';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name',
    ];

    protected $visible = [
        'id', 'name', 'created_at', 'updated_at',
    ];

}
