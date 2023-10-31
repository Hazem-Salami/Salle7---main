<?php

namespace App\Services\Feedback;

use App\Http\Requests\Feedback\FeedbackRequest;
use App\Jobs\feedback\AddFeedbackJob;
use App\Models\Feedback;
use App\Models\Storehouse;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class FeedbackService extends BaseService
{
    /**
     * @param FeedbackRequest
     * @return Response
     */
    public function addFeedback(FeedbackRequest $request): Response
    {
        DB::beginTransaction();

        $evaluator = User::where('email', $request->evaluator_email)->first();

        $storehouse = Storehouse::where('email', $request->evaluator_email)->first();

        if(!$evaluator && !$storehouse){
            return $this->customResponse(false, 'The selected evaluator email is invalid.', null, 400);
        }

        if(!$evaluator)
            $evaluator = $storehouse;

        $user = User::find(auth()->user()->id);

        $feedback = $user->feedbacks()->create([
            'evaluator_email' => $evaluator->email,
            'feedback' => $request->feedback,
        ]);

        $feedback->user_email = $user->email;

        try {
            if($storehouse)
                AddFeedbackJob::dispatch($feedback->toArray())->onQueue('store');
            AddFeedbackJob::dispatch($feedback->toArray())->onQueue('admin');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->customResponse(false, 'Bad Internet', null, 504);
        }
        DB::commit();

        return $this->customResponse(true, 'تم إضافة التقييم بنجاح', $feedback);
    }

    public function getFeedback(): Response
    {
        $user = User::find(auth()->user()->id);

        $feedbacks = Feedback::where('evaluator_email', $user->email)->get();

        $count = Feedback::where('evaluator_email', $user->email)->count();

        $sum = 0;

        foreach ($feedbacks as $feedback){
            $sum += $feedback->feedback;
        }

        if($count == 0)
            return $this->customResponse(true, 'تم إضافة التقييم بنجاح', ['feedback' => 0.0]);

        return $this->customResponse(true, 'تم إضافة التقييم بنجاح', ['feedback' => $sum/$count]);
    }
}
