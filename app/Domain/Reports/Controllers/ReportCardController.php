<?php

namespace App\Domain\Reports\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Reports\Requests\ReportCardRequest;
use App\Domain\Reports\Resources\ReportCardCollection;
use App\Domain\Reports\Resources\ReportCardResource;
use App\Domain\Reports\Services\Contracts\ReportCardServiceInterface;
use App\Domain\Reports\Services\ReportCardPdfBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function __construct(
        protected ReportCardServiceInterface $reportCardService
    ) {}

    public function index()
    {
        return new ReportCardCollection($this->reportCardService->all());
    }

    public function show(int $id)
    {
        return new ReportCardResource($this->reportCardService->find($id));
    }

    public function store(ReportCardRequest $request)
    {
        return new ReportCardResource($this->reportCardService->create($request->validated()));
    }

    public function update(ReportCardRequest $request, int $id)
    {
        return new ReportCardResource($this->reportCardService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->reportCardService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $data = $this->reportCardService->generateReportCard(
            $validated['student_id'],
            $validated['term_id']
        );

        $reportCard = $this->reportCardService->create([
            'student_id' => $data['student_id'],
            'term_id' => $data['term_id'],
            'days_present' => $data['attendance']['present'] ?? 0,
            'days_absent' => $data['attendance']['absent'] ?? 0,
        ]);

        return new ReportCardResource($reportCard);
    }

    public function downloadPdf(int $id, ReportCardPdfBuilder $builder)
    {
        $reportCard = $this->reportCardService->find($id);

        if (!$reportCard) {
            return response()->json(['message' => 'Report card not found.'], 404);
        }

        $pdfData = $builder->build($reportCard);

        $pdf = Pdf::loadView($pdfData['view'], $pdfData['data'])
            ->setPaper('a4', $pdfData['orientation']);

        return $pdf->download($pdfData['filename']);
    }
}
