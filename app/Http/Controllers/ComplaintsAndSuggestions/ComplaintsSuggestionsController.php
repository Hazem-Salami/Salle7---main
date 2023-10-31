<?php

namespace App\Http\Controllers\ComplaintsAndSuggestions;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComplaintsAndSuggestions\ComplaintRequest;
use App\Http\Requests\ComplaintsAndSuggestions\SuggestionRequest;
use App\Services\ComplaintsAndSuggestions\ComplaintsSuggestionsService;
use Illuminate\Http\Response;

class ComplaintsSuggestionsController extends Controller
{
    /**
     *
     * @var ComplaintsSuggestionsService
     */
    protected ComplaintsSuggestionsService $complaintsSuggestionsService;

    // singleton pattern, service container
    public function __construct(ComplaintsSuggestionsService $complaintsSuggestionsService)
    {
        $this->complaintsSuggestionsService = $complaintsSuggestionsService;
    }

    public function addSuggestion(SuggestionRequest $request): Response
    {
        return $this->complaintsSuggestionsService->addSuggestion($request);
    }

    public function addComplaint(ComplaintRequest $request): Response
    {
        return $this->complaintsSuggestionsService->addComplaint($request);
    }

    public function getComplaints(): Response
    {
        return $this->complaintsSuggestionsService->getComplaints();
    }

    public function getSuggestions(): Response
    {
        return $this->complaintsSuggestionsService->getSuggestions();
    }
}
