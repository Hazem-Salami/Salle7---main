<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\FeedbackRequest;
use App\Services\Feedback\FeedbackService;
use Illuminate\Http\Response;

class FeedbackController extends Controller
{
    /**
     *
     * @var FeedbackService
     */
    protected FeedbackService $evaluationService;

    // singleton pattern, service container
    public function __construct(FeedbackService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    public function addFeedback(FeedbackRequest $request): Response
    {
        return $this->evaluationService->addFeedback($request);
    }

    public function getFeedback(): Response
    {
        return $this->evaluationService->getFeedback();
    }
}
