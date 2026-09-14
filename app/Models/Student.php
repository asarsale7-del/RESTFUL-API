<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    

    // use HasFactory;


    protected $fillable = [
        'student_no', 'first_name', 'last_name',
        'course', 'year_level', 'email',
    ];

}
