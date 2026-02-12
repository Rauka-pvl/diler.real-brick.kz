<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\PromoMaterial;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PromoMaterialController extends Controller
{
    public function index()
    {
        $materials = PromoMaterial::orderBy('created_at', 'desc')->paginate(20);

        return view('dealer.promo-materials.index', compact('materials'));
    }

    public function download(PromoMaterial $promoMaterial): StreamedResponse
    {
        if (! Storage::disk('local')->exists($promoMaterial->file_path)) {
            abort(404, 'Файл не найден.');
        }

        return Storage::disk('local')->download(
            $promoMaterial->file_path,
            $promoMaterial->file_name,
            [
                'Content-Type' => $promoMaterial->mime_type ?? 'application/octet-stream',
            ]
        );
    }
}
