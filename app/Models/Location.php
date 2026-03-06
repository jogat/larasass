<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $role
 * @property int $tenant_id
 * @property-read Tenant $tenant
 * @property-read \Illuminate\Database\Eloquent\Relations\Pivot&object{role: string} $pivot
 */
class Location extends Model
{
    protected $fillable = ['name', 'slug', 'tenant_id'];

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return HasMany<LocationUser, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(LocationUser::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'location_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
