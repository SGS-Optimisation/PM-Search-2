<?php

namespace App\Models;

use App\Models\Interfaces\Searchable;
use App\Models\Traits\HasSearchCapability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Collection
 *
 * @property int $id
 * @property string|null $name
 * @property string $search_mode
 * @property int $user_id
 * @property object|null $search_data
 * @property string|null $source_filepath
 * @property string|null $image_path
 * @property array|null $working_data
 * @property array|null $report
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon $consulted_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $search_data_is_ocr
 * @property-read mixed $search_data_is_phrase
 * @property-read mixed $search_data_kv
 * @property-read mixed $search_data_operator
 * @property-read mixed $search_data_search_string
 * @property-read mixed $search_data_type
 * @property-read mixed $title
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereConsultedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSearchData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSearchMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereSourceFilepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereWorkingData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Collection withoutTrashed()
 * @mixin \Eloquent
 */
class Collection extends Model implements Searchable
{
    use SoftDeletes, HasSearchCapability;

    protected $fillable = [
        'name',
        'search_mode',
        'user_id',
        'search_data',
        'source_filepath',
        'image_path',
        'working_data',
        'report',
        'consulted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'search_data' => 'object',
        'working_data' => 'array',
        'report' => 'array',
        'consulted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
