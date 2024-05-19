<?php

namespace Brunocfalcao\Trading\Abstracts;

use Brunocfalcao\LaravelHelpers\Traits\ForModels\HasCustomQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class TradingModel extends Model
{
    use HasCustomQueryBuilder;
    use SoftDeletes;

    protected $guarded = [];

    public function canBeDeleted()
    {
        return true;
    }
}
