<?php

namespace App\Concerns;

trait HasLocalizedAttributes
{
    public function localizedName(): string
    {
        if (app()->getLocale() === 'ar' && filled($this->name_ar ?? null)) {
            return $this->name_ar;
        }

        return $this->name ?? '';
    }

    public function localizedDescription(): ?string
    {
        if (app()->getLocale() === 'ar' && filled($this->description_ar ?? null)) {
            return $this->description_ar;
        }

        return $this->description;
    }
}
