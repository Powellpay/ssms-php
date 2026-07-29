<?php

namespace App\Domain\Attendance\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Attendance\Requests\AttendanceRegisterRequest;
use App\Domain\Attendance\Requests\AttendanceRequest;
use App\Domain\Attendance\Resources\AttendanceCollection;
use App\Domain\Attendance\Resources\AttendanceResource;
use App\Domain\Attendance\Services\Contracts\AttendanceServiceInterface;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceServiceInterface $attendanceService
    ) {}

    public function index()
    {
        return new AttendanceCollection($this->attendanceService->all());
    }

    public function show(int $id)
    {
        return new AttendanceResource($this->attendanceService->find($id));
    }

    public function store(AttendanceRequest $request)
    {
        return new AttendanceResource($this->attendanceService->create($request->validated()));
    }

    public function update(AttendanceRequest $request, int $id)
    {
        return new AttendanceResource($this->attendanceService->update($id, $request->validated()));
    }

    public function destroy(int $id)
    {
        $this->attendanceService->delete($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function register(AttendanceRegisterRequest $request)
    {
        $recordedBy = (int) $request->user()->id;
        $result = $this->attendanceService->markBulkAttendance(
            $request->input('records'),
            (int) $request->input('term_id'),
            $request->input('attendance_date'),
            $recordedBy
        );

        return response()->json([
            'message' => "Attendance recorded for {$result['count']} student(s).",
            'count' => $result['count'],
        ]);
    }

    public function registerShow(Request $request)
    {
        $request->validate([
            'term_id' => 'required|integer|exists:terms,id',
            'attendance_date' => 'required|date',
            'stream_id' => 'nullable|integer|exists:streams,id',
        ]);

        $register = $this->attendanceService->getRegister(
            (int) $request->input('term_id'),
            $request->input('attendance_date'),
            $request->input('stream_id') ? (int) $request->input('stream_id') : null
        );

        return response()->json($register);
    }
}
