<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class FundManager
 *
 * The FundManager class represents a fund manager in the system.
 * It extends the Model class and uses the HasFactory trait.
 *
 * @property int id
 * @property string name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Fund> $funds
 */
class FundManager extends Model
{
    use HasFactory;

    /**
     * Retrieve the funds associated with this object.
     *
     * @return HasMany The funds associated with this object.
     */
    public function funds(): HasMany
    {
        return $this->hasMany(Fund::class);
    }
}
