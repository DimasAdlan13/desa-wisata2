<?php
namespace App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Filament\Resources\ServiceCategoryResource;
use Filament\Resources\Pages\CreateRecord;
class CreateServiceCategory extends CreateRecord
{

    // memberi tahu untuk ngurus resourcenya servicecategory
    protected static string $resource = ServiceCategoryResource::class;
}
