<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * @implements \App\Models\Interfaces\Searchable
 */
trait HasSearchCapability
{


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
