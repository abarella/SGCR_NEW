<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

abstract class AbstractModel extends Model
{
    public function fromDateTime($value)
    {
        $format = 'Y-m-d H:i:s';
        if(env('APP_ENV') === 'production') {
            $format = 'Y-d-m H:i:s';
        }
        return Carbon::parse(parent::fromDateTime($value))->format($format);
    }

}
