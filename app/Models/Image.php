<?php

namespace AppModels;

use IlluminateDatabaseEloquentFactoriesHasFactory;
use IlluminateDatabaseEloquentModel;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'original_name',
    ];
}