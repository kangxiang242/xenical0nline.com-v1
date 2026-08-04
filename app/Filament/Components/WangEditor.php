<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;

class WangEditor extends Field
{
    protected string $view = 'filament.forms.wang-editor';

    protected string $mode = 'default';
    protected string $uploadUrl = '';
    protected array $toolbarButtons = [];

    public function mode(string $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    public function uploadUrl(string $url): static
    {
        $this->uploadUrl = $url;
        return $this;
    }

    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;
        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getUploadUrl(): string
    {
        return $this->uploadUrl ?: url(config('global.admin_path', 'ami3-17drt4-6ne634russ') . '/wang-editor/upload');
    }

    public function getToolbarButtons(): array
    {
        return $this->toolbarButtons;
    }
}