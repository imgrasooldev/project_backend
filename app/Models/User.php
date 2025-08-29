<?php



namespace App\Models;



// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;



class User extends Authenticatable

{

    use HasApiTokens, HasFactory, Notifiable;



    /**

     * The attributes that are mass assignable.

     *

     * @var array<int, string>

     */

    protected $fillable = [

        'name',
        'email',
        'password',
        'phone',
        'user_type_id',
        'city_id',
        'bio',
        'google_id', // Add this
        'avatar',    // Add this
        'last_login_at', // Add this

    ];



    /**

     * The attributes that should be hidden for serialization.

     *

     * @var array<int, string>

     */

    protected $hidden = [

        'password',

        'remember_token',

    ];



    protected function setPasswordAttribute($pass)

    {



        $this->attributes['password'] = bcrypt($pass);

    }

    /**

     * The attributes that should be cast.

     *

     * @var array<string, string>

     */

    protected $casts = [

        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'user_type_id' => 'integer', // Ensure proper casting
        'city_id' => 'integer', // Ensure proper casting

    ];

    public function services()
{
    return $this->hasMany(ServiceProfile::class, 'user_id', 'id');
}

 protected $attributes = [
        'user_type_id' => 1, // Default user type
        'phone' => null,
        'city_id' => null,
        'bio' => null,
    ];
    
    public function deviceTokens()
{
    return $this->hasMany(UserDeviceToken::class);
}

    
}

