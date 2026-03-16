<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Webinar;
use App\User;

class WebinarGrade extends Model
{
    protected $table = 'webinar_grades';
    protected $guarded = [];
    
    // مسار ملف PDF الخاص بالطالب
    public function getPdfPathAttribute()
    {
        return $this->attributes['pdf_path'] ?? null;
    }
 
    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id');
    }

    public function student()
    { 
        return $this->belongsTo(User::class, 'student_id');
    }
}