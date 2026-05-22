<?php
namespace App\Services;

use App\Models\Material;
use Illuminate\Validation\ValidationException;

class MaterialService
{
    public function delete(Material $material): void
    {
        if ($material->status->value === 'in_use') {
            throw ValidationException::withMessages([
                'material' => 'Impossible de supprimer un matériel en cours d\'utilisation.'
            ]);
        }
        $material->delete();
    }
}

?>