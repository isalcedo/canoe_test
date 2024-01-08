<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Represents a company in the application.
 *
 * This class extends the Model class and uses the HasFactory trait.
 * It provides functionality related to managing company data.
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Collection<Fund> $funds
 */
class Company extends Model
{
    use HasFactory;

    public function funds(): BelongsToMany
    {
        return $this->belongsToMany(Fund::class);
    }
}
