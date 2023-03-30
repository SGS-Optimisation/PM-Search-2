<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
 * @mixin \Eloquent

 * @property object|null $search_data
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Search whereSearchMode($value)
 */
class Search extends Model
{

    const SEARCH_MODE_TEXT = 'text';
    const SEARCH_MODE_IMAGE = 'image';

    const SEARCH_SOURCE_FILENAME = 'source.jpg';
    const SEARCH_THUMB_FILENAME = 'thumb.jpg';

    const FLAG_PROCESSED = 'processed';
    const FLAG_HAS_SOURCE_IMAGE = 'source_as_image';
    const FLAG_ERROR = 'error';

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


    // Scopes...

    // Functions ...

    // Relations ...

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

    public function getTitleAttribute()
    {
        if (empty($this->search_data)) {
            return '';
        }
        if ($this->search_mode == static::SEARCH_MODE_TEXT) {
            return Str::slug(implode(' '.$this->search_data->operator.' ', $this->search_data->search_string));
        }
        if ($this->search_mode == static::SEARCH_MODE_IMAGE) {
            return $this->working_data['original_filename'];
        }
    }

    protected function getSearchDataParam($param)
    {
        if (empty($this->search_data)) {
            return 'Empty';
        }

        return $this->search_data->$param ?? 'Empty';
    }

    public function getSearchDataKvAttribute()
    {
        $kv = $this->search_data;

        if ($this->search_mode == static::SEARCH_MODE_TEXT) {
            $kv->search_string = implode(', ', $kv->search_string);
        }

        return $kv;
    }

    public function getSearchDataSearchStringAttribute()
    {
        return implode(', ', $this->search_data->search_string ?? []);
    }

    public function getSearchDataOperatorAttribute()
    {
        return $this->getSearchDataParam('operator');
    }

    public function getSearchDataIsPhraseAttribute()
    {
        return $this->getSearchDataParam('isPhrase');
    }

    public function getSearchDataIsOcrAttribute()
    {
        return $this->getSearchDataParam('isOCR');
    }

    public function getSearchDataTypeAttribute()
    {
        return $this->getSearchDataParam('type');
    }
}
