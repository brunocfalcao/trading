<?php

namespace Brunocfalcao\Trading\Abstracts;

use Brunocfalcao\LaravelHelpers\Traits\ForModels\HasCustomQueryBuilder;
use Illuminate\Database\Eloquent\Model;

abstract class TradingModel extends Model
{
    use HasCustomQueryBuilder;

    protected $guarded = [];

    public function canBeDeleted()
    {
        return true;
    }
}
