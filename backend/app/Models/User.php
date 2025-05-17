<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
        /** @use HasFactory<\Database\Factories\UserFactory> */
        use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

        /**
         * The attributes that are mass assignable.
         *
         * @var list<string>
         */

        protected $fillable = [
            'name', 'username', 'password', 'email', 'phone', 'dob', 'mobile', 'role_id', 'image', 'created_by', 'status'
        ];

        public function role()
        {
            return $this->belongsTo(Role::class);
        }

        public function createdBy()
        {
            return $this->belongsTo(User::class, 'created_by');
        }

        public function deletedBy()
        {
            return $this->belongsTo(User::class, 'deleted_by');
        }

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
}
