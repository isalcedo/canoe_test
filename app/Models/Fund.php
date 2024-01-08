<?php

namespace App\Models;

use App\Events\FundDuplication;
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
 * @property int $fund_manager_id
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

    public $guarded = [
        'id'
    ];
    public $casts = [
        'aliases' => 'array'
    ];

    /**
     * Since there were no instructions in the test about the logic for Fund creations,
     * and because, following the instructions, we are not preventing the creation of duplicate Funds,
     * I will use this Laravel event to "announce" the possibility of duplication.
     */
    public static function booted(): void
    {
        static::saving(static function (Fund $fund) {
            $currentFund = Fund::where(['fund_manager_id' => $fund->fund_manager_id])
                ->where(['name' => $fund->name])->orWhere(static function($query) use ($fund) {
                    foreach ($fund->aliases as $alias) {
                        $query->orWhereJsonContains('aliases', $alias);
                    }
            })->first();

            if ($currentFund) {
                event(new FundDuplication($fund));
            }
        });
    }

    /**
     * Retrieve the manager for this model.
     *
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(FundManager::class, 'fund_manager_id');
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }
}
