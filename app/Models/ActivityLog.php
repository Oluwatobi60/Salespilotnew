<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Staffs;


class ActivityLog extends Model

{
    protected $fillable = [
        'user_id',
        'staff_id',
        'action',
        'device',
        'ip_address',
        'details',
    ];


     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


        public function staff()
        {
            return $this->belongsTo(Staffs::class, 'staff_id', 'staff_id');
        }
}
