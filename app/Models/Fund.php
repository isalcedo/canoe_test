<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Fund
 *
 * This class represents a fund and extends the Model class.
 *
 * @property int id
 * @property string name
 * @property array $aliases
 * @property int $year
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property FundManager $manager
 * @property Collection<Company> $companies
 */
class Fund extends Model
{
    use HasFactory;

    public $casts = [
        'aliases' => 'array'
    ];

    /**
     * Retrieve the manager for this model.
     *
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(FundManager::class,  'fund_manager_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
}
