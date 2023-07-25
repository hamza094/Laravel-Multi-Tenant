<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FilterByTenant;

class Project extends Model
{
    use HasFactory,FilterByTenant;

    protected $guarded = [];

}
