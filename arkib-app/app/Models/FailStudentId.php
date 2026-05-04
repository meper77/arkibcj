<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailStudentId extends Model
{
    protected $table = 'fail_student_ids';

    protected $fillable = ['fail_id', 'student_id'];

    public $timestamps = true;

    public function fail(): BelongsTo
    {
        return $this->belongsTo(Fail::class, 'fail_id');
    }
}
