<?php

namespace App\Models;

use App\Models\Interfaces\Searchable;
use App\Models\Traits\HasSearchCapability;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Search
 *
 * @property int $id
 * @property int $user_id
 * @property string $source_filepath
 * @property string $image_path
 * @property array $working_data
 * @property array $report
 * @property string $search_mode
 * @property int $created_at
 * @property int $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Search newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Search query()
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereWorkingData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSourceFilepath($value)
 * @property object|null $search_data
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchMode($value)
 * @property int|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Search> $children
 * @property-read int|null $children_count
 * @property-read mixed $search_data_is_ocr
 * @property-read mixed $search_data_is_phrase
 * @property-read mixed $search_data_kv
 * @property-read mixed $search_data_operator
 * @property-read mixed $search_data_search_string
 * @property-read mixed $search_data_type
 * @property-read mixed $title
 * @property-read Search|null $parent
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereParentId($value)
 * @mixin \Eloquent
 */
class Search extends Model implements Searchable
{
    use HasSearchCapability;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'search_mode',
        'parent_id',
        'user_id',
        'search_data',
        'source_filepath',
        'image_path',
        'working_data',
        'report',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'search_data' => 'object',
        'working_data' => 'array',
        'report' => 'array',
    ];

    public function scopeModeText(Builder $query)
    {
        $query->where('search_mode', 'text');
    }

    public function scopeModeImage(Builder $query)
    {
        $query->where('search_mode', 'image');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Search::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Search::class, 'parent_id');
    }


}
