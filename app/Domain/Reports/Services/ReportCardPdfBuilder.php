<?php

namespace App\Domain\Reports\Services;

use App\Domain\Assessment\Models\GradingScale;
use App\Domain\Assessment\Models\GenericSkillRating;
use App\Domain\Assessment\Models\SubjectTermResult;
use App\Domain\Reports\Models\ReportCard;

class ReportCardPdfBuilder
{
    public function build(ReportCard $reportCard): array
    {
        $reportCard->load([
            'student.user',
            'term',
            'stream.classLevel',
        ]);

        $studentId = $reportCard->student_id;
        $termId = $reportCard->term_id;

        $subjectResults = SubjectTermResult::with('subject')
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $skillRatings = GenericSkillRating::with(['genericSkill', 'rating'])
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $gradingScale = GradingScale::where('school_id', $reportCard->school_id)
            ->orderBy('min_score')
            ->get();

        $formattedResults = $subjectResults->map(function ($result) use ($gradingScale) {
            $total = ($result->ca_score ?? 0) + ($result->eot_score ?? 0);
            $grade = $this->computeGrade($total, $gradingScale);

            return [
                'subject' => $result->subject?->subject_name ?? 'N/A',
                'subject_code' => $result->subject?->subject_code ?? '',
                'ca_score' => number_format((float) ($result->ca_score ?? 0), 2),
                'eot_score' => number_format((float) ($result->eot_score ?? 0), 2),
                'total' => number_format($total, 2),
                'grade' => $grade['grade'] ?? '-',
                'remark' => $grade['remarks'] ?? '',
                'teacher_comment' => $result->subject_teacher_comment ?? '',
            ];
        });

        $formattedSkills = $skillRatings->map(function ($rating) {
            return [
                'skill_name' => $rating->genericSkill?->skill_name ?? 'N/A',
                'rating' => $rating->rating?->rating_label ?? '-',
                'rating_code' => $rating->rating?->rating_code ?? '-',
                'remarks' => $rating->remarks ?? '',
            ];
        });

        $studentName = $reportCard->student
            ? trim(($reportCard->student->first_name ?? '') . ' ' . ($reportCard->student->last_name ?? ''))
            : 'N/A';

        $studentName = $studentName ?: ($reportCard->student?->user?->name ?? 'N/A');

        $class = $reportCard->stream?->classLevel
            ? ($reportCard->stream->classLevel->name ?? '')
            : '';

        $stream = $reportCard->stream?->stream_name ?? '';

        $data = [
            'report_card' => $reportCard,
            'student_name' => $studentName,
            'admission_no' => $reportCard->student?->admission_no ?? 'N/A',
            'class' => trim($class . ' ' . $stream),
            'term' => $reportCard->term?->term_name ?? 'N/A',
            'academic_year' => $reportCard->term?->academicYear?->name ?? '',
            'subject_results' => $formattedResults,
            'skill_ratings' => $formattedSkills,
            'days_present' => $reportCard->days_present ?? 0,
            'days_absent' => $reportCard->days_absent ?? 0,
            'class_teacher_comment' => $reportCard->class_teacher_comment ?? '',
            'head_teacher_comment' => $reportCard->head_teacher_comment ?? '',
            'next_term_begins' => $reportCard->next_term_begins?->format('F j, Y') ?? 'TBD',
            'date_issued' => $reportCard->date_issued?->format('F j, Y') ?? now()->format('F j, Y'),
        ];

        $filename = $this->sanitizeFilename(
            "report_card_{$studentName}_{$reportCard->term?->term_name}_{$reportCard->id}.pdf"
        );

        return [
            'view' => 'reports.report-card',
            'data' => $data,
            'filename' => $filename,
            'orientation' => 'portrait',
        ];
    }

    private function computeGrade(float $score, $gradingScale): array
    {
        foreach ($gradingScale as $scale) {
            if ($score >= (float) $scale->min_score && $score <= (float) $scale->max_score) {
                return [
                    'grade' => $scale->grade,
                    'remarks' => $scale->remarks ?? $scale->descriptor ?? '',
                ];
            }
        }

        return ['grade' => '-', 'remarks' => ''];
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\(\) ]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);

        return $filename;
    }
}
