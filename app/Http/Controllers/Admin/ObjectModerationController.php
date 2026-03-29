<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectObject;
class ObjectModerationController extends Controller
{
    public function index()
    {
        $items = ProjectObject::query()
            ->where('moderation_status', ProjectObject::MODERATION_PENDING)
            ->with(['dealer', 'duplicateOf', 'duplicateOf.dealer', 'client'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.object-moderation.index', compact('items'));
    }

    public function show(int $object)
    {
        $objectModel = ProjectObject::query()
            ->where('moderation_status', ProjectObject::MODERATION_PENDING)
            ->with([
                'dealer',
                'client',
                'duplicateOf',
                'duplicateOf.dealer',
                'duplicateOf.client',
                'objectProducts',
            ])
            ->findOrFail($object);

        return view('admin.object-moderation.show', ['object' => $objectModel]);
    }

    public function approve(int $object)
    {
        $obj = ProjectObject::query()
            ->where('moderation_status', ProjectObject::MODERATION_PENDING)
            ->findOrFail($object);

        $obj->update([
            'moderation_status' => null,
            'duplicate_of_project_object_id' => null,
        ]);

        return redirect()
            ->route('admin.moderation.objects.index')
            ->with('success', 'Заявка утверждена, объект активирован.');
    }

    public function reject(int $object)
    {
        $obj = ProjectObject::query()
            ->where('moderation_status', ProjectObject::MODERATION_PENDING)
            ->findOrFail($object);

        $obj->update(['moderation_status' => ProjectObject::MODERATION_REJECTED]);

        return redirect()
            ->route('admin.moderation.objects.index')
            ->with('success', 'Заявка отклонена.');
    }
}
