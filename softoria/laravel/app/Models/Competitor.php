<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    protected $fillable = ['target_domain', 'referring_domain', 'excluded_target', 'rank', 'backlinks'];
}
