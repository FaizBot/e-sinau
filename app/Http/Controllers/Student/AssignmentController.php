<?php

namespace App\Http\Controllers\Student;

use App\Models\Answer;
use App\Models\Course;
use App\Models\Result;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function show(SchoolClass $class, Course $course, Assignment $assignment)
    {
        $title = 'Soal Tugas - ' . $assignment->title;
        $assignment->load('questions.option');

        $isTeacher = in_array(Auth::user()->role, ['admin', 'teacher']);

        return view('student.task.assignments.show', compact(
            'title',
            'class',
            'course',
            'assignment',
            'isTeacher'
        ));
    }

    public function submitAssignment(Request $request, SchoolClass $class, Course $course, Assignment $assignment)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'answers' => 'required|array|min:1',
            ]);

            $student = Auth::user()->student;
            $answers = $request->answers;

            $totalScore = 0;
            $points = [];

            // Simpan jawaban mentah
            Answer::create([
                'student_id' => $student->id,
                'assignment_id' => $assignment->id,
                'student_answer' => json_encode($answers),
            ]);

            $questions = $assignment->questions()->with('option')->get();

            foreach ($questions as $question) {

                $userAnswer = $answers[$question->id] ?? null;
                $earned = 0;

                if ($question->question_type === 'multiple_choice') {
                    $correct = optional($question->option)->correct_option;

                    if ($correct !== null && $userAnswer === $correct) {
                        $earned = $question->point;
                    }
                }

                elseif ($question->question_type === 'essay') {
                    if ($assignment->corrected === 'system') {
                        $correct = optional($question->option)->correct_option;

                        if ($correct && strtolower($correct) === strtolower($userAnswer)) {
                            $earned = $question->point;
                        }
                    }
                    // kalau corrected = teacher → 0 dulu
                }

                $totalScore += $earned;
                $points[$question->id] = $earned;
            }

            Result::create([
                'student_id' => $student->id,
                'assignment_id' => $assignment->id,
                'points' => json_encode($points),
                'total_score' => $totalScore,
                'status' => $assignment->corrected === 'system' ? 'completed' : 'pending',
            ]);

            DB::commit();

            return redirect()
                ->route('student.assignment.result', [$class, $course, $assignment->id])
                ->with('success', 'Jawaban berhasil dikirim!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function result($class, $course, Assignment $assignment)
    {
        $title = 'Hasil Tugas - ' . $assignment->title;
        $student = Auth::user()->student;

        $result = Result::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $answer = Answer::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $studentAnswers = json_decode($answer->student_answer ?? '{}', true);
        $points = json_decode($result->points ?? '{}', true);

        $questions = $assignment->questions()->with('option')->get();

        return view('student.task.assignments.result', compact(
            'title',
            'class',
            'course',
            'assignment',
            'questions',
            'student',
            'result',
            'studentAnswers',
            'points'
        ));
    }
}
