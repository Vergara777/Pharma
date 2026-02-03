<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

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
        'address',
        'position',
        'hire_date',
        'birth_date',
        'id_number',
        'role',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'hire_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        // Si tiene avatar personalizado, usarlo
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            // Usar una URL con timestamp único para evitar caché
            return url('storage/' . $this->avatar) . '?t=' . now()->timestamp . rand(1000, 9999);
        }
        
        // Fallback a las iniciales generadas por Jetstream
        return $this->profile_photo_url;
    }

    /**
     * Override Jetstream's profile photo URL to use our custom avatar.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
            // Usar una URL con timestamp único para evitar caché
            return url('storage/' . $this->avatar) . '?t=' . now()->timestamp . rand(1000, 9999);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the user's name for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }

    /**
     * Determine if the user can access Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the user's role label for display.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => '👑 Administrador',
            'tech' => '👨‍💼 Trabajador',
            default => 'Usuario',
        };
    }
}
