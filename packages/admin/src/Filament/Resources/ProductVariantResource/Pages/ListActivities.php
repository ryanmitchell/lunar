<?php

namespace Lunar\Admin\Filament\Resources\ProductVariantResource\Pages;

use Lunar\Admin\Filament\Resources\ProductVariantResource;
use Lunar\Admin\Support\Pages\BaseActivityLog;

class ListActivities extends BaseActivityLog
{
    protected static string $resource = ProductVariantResource::class;

    public function getBreadcrumbs(): array
    {
        return [
            ...ProductVariantResource::getBaseBreadcrumbs(
                $this->getRecord()
            ),
            ProductVariantResource::getUrl('inventory', [
                'record' => $this->getRecord(),
            ]) => $this->getTitle(),
        ];
    }
}
