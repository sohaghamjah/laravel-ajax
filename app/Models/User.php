<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const VALIDATION_RULES = [
        'role_id'               => ['required', 'integer'],
        'name'                  => ['required', 'string'],
        'email'                 => ['required', 'email', 'unique:users,email'],
        'mobile_no'             => ['required', 'unique:users,mobile_no'],
        'avatar'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp'],
        'district_id'           => ['required', 'integer'],
        'upazila_id'            => ['required', 'integer'],
        'postal_code'           => ['required', 'numeric'],
        'address'               => ['required', 'string'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'avatar',
        'mobile_no',
        'district_id',
        'upazila_id',
        'postal_code',
        'address',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }
    public function district(){
        return $this->belongsTo(Location::class, 'district_id', 'id');
    }
    public function upazila(){
        return $this->belongsTo(Location::class, 'upazila_id', 'id');
    }


    // User data filter property
    private $name;
    private $email;
    private $mobile_no;
    private $district_id;
    private $upazila_id;
    private $role_id;
    private $status;

    // User data filter method

    public function setName($name){
        $this->name = $name;
    }
    public function setEmail($email){
        $this->email = $email;
    }
    public function setMobileNo($mobile_no){
        $this->mobile_no = $mobile_no;
    }
    public function setDistrictId($district_id){
        $this->district_id = $district_id;
    }
    public function setUpazilaId($upazila_id){
        $this->upazila_id = $upazila_id;
    }
    public function setRoleId($role_id){
        $this->role_id = $role_id;
    }
    public function setStatus($status){
        $this->status = $status;
    }

    // user list property
    private $order = array('users.id' => 'desc');
    private $column_order;

    private $orderValue;
    private $dirValue;
    private $lengthValue;
    private $startValue;

    // User list function
    public function setOrderValue($orderValue){
        $this->orderValue = $orderValue;
    }

    public function setDirValue($dirValue){
        $this->dirValue = $dirValue;
    }

    public function setLengthValue($lengthValue){
        $this->lengthValue = $lengthValue;
    }

    public function setStartValue($startValue){
        $this->startValue = $startValue;
    }

    // user list query
    private function getDataTableQuery(){
        // column wise sorting
        $this->column_order = ['users.id','','users.name','users.role_id','users.email','users.mobile_no','users.district_id','users.upazila_id','users.postal_code','users.email_verified_at','users.status',''];
        // user list query
        $query = self::with(['role:id,role_name', 'district:id,location_name', 'upazila:id,location_name']);
        // user data filter query
        if(!empty($this->name)){
            $query->where('users.name', 'like','%' .$this->name. '%');
        }
        if(!empty($this->email)){
            $query->where('users.email', 'like','%' .$this->email. '%');
        }
        if(!empty($this->mobile_no)){
            $query->where('users.mobile_no' ,'like','%' .$this->mobile_no. '%');
        }
        if(!empty($this->district_id)){
            $query->where('users.district_id',$this->district_id);
        }
        if(!empty($this->upazila_id)){
            $query->where('users.upazila_id',$this->upazila_id);
        }
        if(!empty($this->role_id)){
            $query->where('users.role_id',$this->role_id);
        }
        if(!empty($this->status)){
            $query->where('users.status',$this->status);
        }

        // Sorting
        if(isset($this->orderValue) && isset($this->dirValue)){
            $query->orderBy($this->column_order[$this->orderValue], $this->dirValue);
        }else if(isset($this->order)){
            $query->orderBy(key($this->order), $this->order[key($this->order)]);
        }
        return $query;
    }

    public function getList()
    {
        $query = $this->getDataTableQuery();
        if ($this->lengthValue != -1) {
            $query->offset($this->startValue)->limit($this->lengthValue);
        }
        return $query->get();
    }

    // count function
    public function countFilter(){
        $query = $this->getDataTableQuery();
        return $query->get()->count();
    }

    public function countAll(){
        return self::toBase()->get()->count();
    }


}
