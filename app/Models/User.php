<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send a password reset notification to the user.
     */
    public function sendPasswordResetNotification($token): void
    {
        // Password::sendResetLink(...)
        // →broker が User を取得
        // →$user->sendPasswordResetNotification($token)
        //     User モデルで override していればそっちが呼ばれる←今ココ
        // →$this->notify(new CustomResetPasswordNotification($token))
        // →CustomResetPasswordNotification::toMail()
        $this->notify(new CustomResetPasswordNotification($token));
    }

}
