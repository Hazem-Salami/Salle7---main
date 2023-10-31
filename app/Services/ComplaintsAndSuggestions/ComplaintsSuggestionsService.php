<?php

namespace App\Services\ComplaintsAndSuggestions;

use App\Http\Requests\ComplaintsAndSuggestions\ComplaintRequest;
use App\Http\Requests\ComplaintsAndSuggestions\SuggestionRequest;
use App\Jobs\complaintsAndSuggestions\AddComplaintJob;
use App\Jobs\complaintsAndSuggestions\AddSuggestionJob;
use App\Models\Complaint;
use App\Models\Suggestion;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ComplaintsSuggestionsService extends BaseService
{
    /**
     *
     * @return Response
     */
    public function addSuggestion(SuggestionRequest $request): Response
    {
        DB::beginTransaction();

        $user = User::find(auth()->user()->id);

        $suggestion = $user->suggestions()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        $suggestion->user_email = $user->email;

        try {
            AddSuggestionJob::dispatch($suggestion->toArray())->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();

        return $this->customResponse(true, 'تم إضافة الأقتراح بنجاح', $suggestion);
    }

    /**
     *
     * @return Response
     */
    public function addComplaint(ComplaintRequest $request): Response
    {
        DB::beginTransaction();

        $user = User::find(auth()->user()->id);

        $complaint = $user->complaints()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);
        $complaint->user_email = $user->email;

        try {
            AddComplaintJob::dispatch($complaint->toArray())->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();

        return $this->customResponse(true, 'تم إضافة الشكوى بنجاح', $complaint);
    }

    public function getSuggestions(): Response
    {
        $suggestions = Suggestion::where('user_id', auth()->user()->id)->paginate(\request('size'));

        return $this->customResponse(true, 'تم الحصول على المقترحات', $suggestions);
    }

    public function getComplaints(): Response
    {
        $complaints = Complaint::where('user_id', auth()->user()->id)->paginate(\request('size'));

        return $this->customResponse(true, 'تم الحصول على الشكاوي', $complaints);
    }

}
