<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class device extends Model
{
    use HasFactory, HasApiTokens;
    use \App\Traits\TraitUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type',
        'imei',
        'sim_number',
        'location',
    ];
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_device_relations', 'device_uuid', 'owner_id')->withPivot('role');
    }
}
