<?php
namespace App\Services;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function delete(Category $category): void
    {
        if ($category->materials()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Impossible de supprimer une catégorie liée à des matériels.'
            ]);
        }
        $category->delete();
    }
}

?>