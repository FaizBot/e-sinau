<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Option;
use App\Models\Result;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CourseSchoolClass;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    // public function getAssignmentsByCourse(Course $course)
    // {
    //     $user = Auth::user();

    //     $title = 'Daftar Tugas';

    //     if ($user->role === 'admin') {
    //         $admin = User::where('id', $user->id)->firstOrFail();

    //         $classes = $course->schoolClasses;

    //         $class = CourseSchoolClass::where('course_id', $course->id)->first();

    //         $assignments = $course->assignments;

    //         return view('admin.assignment', compact('course', 'classes', 'assignments', 'class', 'title'));
    //     }

    //     // if ($user->role === 'teacher') {
    //     //     $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

    //     //     if ($course->teacher_id !== $teacher->id) {
    //     //         abort(403, 'Unauthorized akses course ini.');
    //     //     }

    //     //     $classes = $course->schoolClasses;
    //     //     $assignments = $course->assignments;

    //     //     return view('admin.assignment', compact('course', 'classes', 'assignments'));
    //     // }

    //     // if ($user->role === 'student') {
    //     //     $assignments = $course->assignments;
    //     //     return view('assignments.student_index', compact('course', 'assignments'));
    //     // }
    // }

    public function create(SchoolClass $class, Course $course)
    {
        $title = 'Tambah Tugas';
        return view('admin.task.assignments.create', compact('course', 'class', 'title'));
    }

    public function store(Request $request, SchoolClass $class, Course $course)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'corrected' => 'required|in:system,teacher',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.point' => 'required|integer|min:1',
            'questions.*.question_type' => 'required|in:multiple_choice,essay',
            'questions.*.correct_option' => 'nullable',
            'questions.*.options' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // ================= ASSIGNMENT =================
            $assignment = Assignment::create([
                'course_id'       => $course->id,
                'school_class_id' => $class->id, // 🔥 dari route
                'title'           => $request->title,
                'slug'            => Str::slug($request->title) . '-' . time(),
                'description'     => $request->description,
                'corrected'       => $request->corrected,
                'is_published'    => false, // draft (e-Sinau style)
                'order_number'    => Assignment::where('course_id', $course->id)
                                        ->max('order_number') + 1,
            ]);

            // ================= QUESTIONS =================
            foreach ($request->questions as $qData) {

                $question = Question::create([
                    'assignment_id' => $assignment->id,
                    'question_text' => $qData['text'],
                    'question_type' => $qData['question_type'],
                    'point'         => $qData['point'],
                ]);

                // ============ MULTIPLE CHOICE ============
                if ($qData['question_type'] === 'multiple_choice') {

                    $options = $qData['options'] ?? [];
                    $correctIndex = $qData['correct_option'] ?? null;

                    Option::create([
                        'question_id'    => $question->id,
                        'option_text'    => json_encode($options),
                        'correct_option' => isset($options[$correctIndex]['text'])
                            ? $options[$correctIndex]['text']
                            : null,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.tasks.index', [$class->id, $course->id])
                ->with('success', 'Tugas berhasil ditambahkan');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan tugas');
        }
    }

    public function destroy(SchoolClass $class, Course $course, Assignment $assignment)
    {
        // Validasi nested route
        if (
            $assignment->course_id !== $course->id ||
            $assignment->school_class_id !== $class->id
        ) {
            abort(404);
        }

        try {
            $assignment->delete();

            return redirect()
                ->route('admin.courses.tasks.index', [$class->id, $course->id])
                ->with('success', 'Tugas berhasil dihapus');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus tugas');
        }
    }

    public function show(SchoolClass $class, Course $course, Assignment $assignment)
    {
        $assignment->load('questions.option');

        $isTeacher = in_array(Auth::user()->role, ['admin', 'teacher']);

        return view('admin.task.assignments.show', compact(
            'class',
            'course',
            'assignment',
            'isTeacher'
        ));
    }

    public function togglePublish(SchoolClass $class, Course $course, Assignment $assignment)
    {
        $assignment->update([
            'is_published' => !$assignment->is_published
        ]);

        return back()->with(
            'success',
            $assignment->is_published
                ? 'Tugas berhasil dipublikasikan.'
                : 'Tugas berhasil disembunyikan.'
        );
    }

    public function progress(SchoolClass $class, Course $course, Assignment $assignment)
    {
        if (
            $assignment->course_id !== $course->id ||
            $assignment->school_class_id !== $class->id
        ) {
            abort(404);
        }

        $title = 'Progress Tugas: ' . $assignment->title;

        $assignment->load('schoolClass.students.user');

        $results = Result::where('assignment_id', $assignment->id)
            ->get()
            ->keyBy('student_id');

        $students = $assignment->schoolClass->students->map(function ($student) use ($results) {
            $result = $results->get($student->id);

            return (object) [
                'nis' => $student->nis,
                'name' => $student->user->name,
                'status' => $result->status ?? 'belum_mengerjakan',
                'score' => $result->total_score ?? null,
                'can_grade' => $result && $result->status === 'pending'
            ];
        });

        return view('admin.task.assignments.progress', compact(
            'title',
            'class',
            'course',
            'assignment',
            'students'
        ));
    }

    public function showAssignmentStudent(SchoolClass $class, Course $course, Assignment $assignment, $nis)
    {
        $title = 'Review Tugas Siswa: ' . $assignment->title;
        $student = Student::where('nis', $nis)->firstOrFail();

        $answer = Answer::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $result = Result::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        // 🔑 decode JSON SEKALI di controller
        $studentAnswers = json_decode($answer->student_answer ?? '{}', true);
        $points = json_decode($result->points ?? '{}', true);

        // soal + option
        $questions = $assignment->questions()
            ->with('option')
            ->orderBy('id')
            ->get();

        return view('admin.task.assignments.review', compact(
            'title',
            'class',
            'course',
            'assignment',
            'student',
            'questions',
            'result',
            'studentAnswers',
            'points'
        ));
    }

    public function correctAssignmentStudent(
    SchoolClass $class,
    Course $course,
    Assignment $assignment,
    $nis
) {
    $title = 'Koreksi Tugas Siswa: ' . $assignment->title;
    abort_if($assignment->corrected !== 'teacher', 403);

    $student = Student::where('nis', $nis)->firstOrFail();

    $answer = Answer::where('assignment_id', $assignment->id)
        ->where('student_id', $student->id)
        ->firstOrFail();

    // 🔥 DECODE DI SINI
    $studentAnswers = json_decode($answer->student_answer, true) ?? [];

    $result = Result::firstOrCreate(
        [
            'assignment_id' => $assignment->id,
            'student_id'    => $student->id,
        ],
        [
            'points'      => [],
            'total_score' => 0,
            'status'      => 'pending',
        ]
    );

    $questions = $assignment->questions()->with('option')->get();

    return view('admin.task.assignments.grade', compact(
        'title',
        'class',
        'course',
        'assignment',
        'student',
        'answer',
        'studentAnswers', // 🔥 KIRIM
        'result',
        'questions'
    ));
}

    public function updateAssignmentStudent(
    Request $request,
    SchoolClass $class,
    Course $course,
    Assignment $assignment,
    $nis
) {
    abort_if($assignment->corrected !== 'teacher', 403);

    $student = Student::where('nis', $nis)->firstOrFail();

    $answer = Answer::where('assignment_id', $assignment->id)
        ->where('student_id', $student->id)
        ->firstOrFail();

    $studentAnswers = json_decode($answer->student_answer, true);

    $questions = $assignment->questions()->with('option')->get();

    $request->validate([
        'points'   => 'required|array',
        'points.*' => 'nullable|integer|min:0',
    ]);

    $points = [];
    $totalScore = 0;

    foreach ($questions as $question) {

        if ($question->question_type === 'multiple_choice') {

            $userAnswer = $studentAnswers[$question->id] ?? null;
            $correct = optional($question->option)->correct_option;

            $score = ($correct !== null && $userAnswer === $correct)
                ? (int) $question->point
                : 0;

        } else {

            $score = (int) ($request->points[$question->id] ?? 0);
            $score = min($score, $question->point);
        }

        $points[$question->id] = $score;
        $totalScore += $score;
    }

    Result::updateOrCreate(
        [
            'assignment_id' => $assignment->id,
            'student_id'    => $student->id,
        ],
        [
            'points'      => json_encode($points),
            'total_score' => $totalScore,
            'status'      => 'completed',
        ]
    );

    return redirect()
        ->route('admin.assignment.progress', [
            'class'      => $class->id,
            'course'     => $course->id,
            'assignment' => $assignment->id,
        ])
        ->with('success', 'Nilai berhasil disimpan');
}

    // public function submitAssignment(Request $request, Assignment $assignment)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $request->validate([
    //             'answers' => 'required|array|min:1',
    //             'answers.*' => 'required|string'
    //         ]);

    //         $student = Auth::user()->student; // Pastikan relasi 'student' tersedia di User model
    //         $answers = $request->answers;
    //         $totalScore = 0;
    //         $points = [];

    //         Answer::create([
    //             'student_id' => $student->id,
    //             'assignment_id' => $assignment->id,
    //             'student_answer' => json_encode($answers),
    //         ]);

    //         $questions = Question::where('assignment_id', $assignment->id)->with('options')->get();

    //         foreach ($questions as $index => $question) {
    //             $userAnswer = $answers[$index] ?? null;
    //             $earned = 0;
    //             $option = $question->options->first(); // ambil satu opsi pertama

    //             if ($question->question_type === 'multiple_choice') {
    //                 if ($option && $option->correct_option === $userAnswer) {
    //                     $earned = $question->point;
    //                 }
    //             } elseif ($question->question_type === 'essay' && $assignment->corrected === 'teacher') {
    //                 // Untuk soal essay yang dikoreksi oleh guru, kita tidak bisa menentukan poin di sini
    //                 // Poin akan diisi nanti oleh guru saat mengoreksi
    //                 $earned = 0;
    //             } elseif ($question->question_type === 'essay' && $assignment->corrected === 'system') {
    //                 if (strtolower(optional($question->options)->correct_option) === strtolower($userAnswer)) {
    //                     $earned = $question->point;
    //                 }
    //             }

    //             $totalScore += $earned;
    //             $points[] = $earned;
    //         }

    //         $status = $assignment->corrected === 'system' ? 'completed' : 'pending';
    //         Result::create([
    //             'student_id' => $student->id,
    //             'assignment_id' => $assignment->id,
    //             'points' => json_encode($points),
    //             'total_score' => $totalScore,
    //             'status' => $status,
    //         ]);

    //         DB::commit();

    //         return redirect()->route('student.assignment.result', $assignment->slug)
    //             ->with('success', 'Jawaban berhasil dikirim!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    // public function viewAnswers(Assignment $assignment)
    // {
    //     $assignment->load('schoolClass.students');
    //     $students = $assignment->schoolClass->students;

    //     $results = Result::where('assignment_id', $assignment->id)
    //         ->get()
    //         ->keyBy('student_id');

    //     $data = $students->map(function ($student) use ($results) {
    //         $result = $results->get($student->id);

    //         return (object) [
    //             'nis' => $student->nis,
    //             'student_name' => $student->user->name,
    //             'status' => $result->status ?? 'not_submitted',
    //             'total_score' => $result->total_score ?? null,
    //         ];
    //     });

    //     return view('admin.assignments.answers.index', [
    //         'assignment' => $assignment,
    //         'students' => $data
    //     ]);
    // }

    // public function gradeAssignment(Request $request, Assignment $assignment, Answer $answer)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $request->validate([
    //             'points' => 'required|array|min:1',
    //             'points.*' => 'required|numeric'
    //         ]);

    //         $result = Result::where('student_id', $answer->student_id)
    //             ->where('assignment_id', $assignment->id)
    //             ->firstOrFail();

    //         $points = $request->points;
    //         $totalScore = array_sum($points);

    //         $result->update([
    //             'points' => json_encode($points),
    //             'total_score' => $totalScore,
    //             'status' => 'completed'
    //         ]);

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Jawaban berhasil dikoreksi!');
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return redirect()->back()->with('error', 'Gagal mengoreksi: ' . $e->getMessage());
    //     }
    // }

    // public function viewSubmited(Assignment $assignment)
    // {
    //     $title = 'Jawaban Anda: ' . $assignment->title;

    //     // Ambil siswa berdasarkan user yang sedang login
    //     $student = Student::where('user_id', Auth::id())->firstOrFail();

    //     // Ambil jawaban siswa
    //     $answer = Answer::where('student_id', $student->id)
    //         ->where('assignment_id', $assignment->id)
    //         ->first();

    //     if (!$answer) {
    //         return redirect()->back()->with('error', 'Jawaban siswa tidak ditemukan.');
    //     }

    //     // Ambil hasil penilaian (jika ada)
    //     $result = Result::where('student_id', $student->id)
    //         ->where('assignment_id', $assignment->id)
    //         ->first();

    //     // Decode jawaban & poin
    //     $studentAnswers = json_decode($answer->student_answer, true);
    //     $points = $result ? json_decode($result->points, true) : [];

    //     // Ambil pertanyaan dan opsi
    //     $assignment->load(['questions.options']);

    //     $questions = $assignment->questions->map(function ($question, $index) use ($studentAnswers, $points) {
    //         return (object) [
    //             'id' => $question->id,
    //             'question_text' => $question->question_text,
    //             'question_type' => $question->question_type,
    //             'options' => $question->options->isNotEmpty()
    //                 ? json_decode($question->options->first()->option_text, true)
    //                 : [],
    //             'correct_option' => $question->question_type === 'multiple_choice'
    //                 ? optional($question->options->first())->correct_option
    //                 : null,
    //             'student_answer' => $studentAnswers[$index] ?? null,
    //             'point' => $question->point,
    //             'earned_point' => $points[$index] ?? null,
    //         ];
    //     });

    //     return view('student.assignment-results', compact('assignment', 'student', 'questions', 'result', 'title'));
    // }

    // public function viewUserResult(Assignment $assignment)
    // {
    //     $student = Student::where('user_id', Auth::id())->first();

    //     if (!$student) {
    //         return redirect()->back()->with('error', 'User tidak ditemukan sebagai siswa.');
    //     }

    //     $answer = Answer::where('student_id', $student->id)
    //         ->where('assignment_id', $assignment->id)
    //         ->first();

    //     if (!$answer) {
    //         return redirect()->back()->with('error', 'Jawaban tidak ditemukan.');
    //     }

    //     $result = Result::where('student_id', $student->id)
    //         ->where('assignment_id', $assignment->id)
    //         ->first();

    //     if (!$result) {
    //         return redirect()->back()->with('error', 'Hasil tidak ditemukan.');
    //     }

    //     $questions = Question::where('assignment_id', $assignment->id)
    //         ->with('options')
    //         ->get();

    //     $studentAnswers = json_decode($answer->student_answer, true);
    //     $earnedPoints = json_decode($result->points, true);

    //     $questionData = $questions->map(function ($question, $index) use ($studentAnswers, $earnedPoints) {
    //         return (object) [
    //             'id' => $question->id,
    //             'question_text' => $question->question_text,
    //             'question_type' => $question->question_type,
    //             'point' => $question->point,
    //             'student_answer' => $studentAnswers[$index] ?? null,
    //             'earned_point' => $earnedPoints[$index] ?? 0,
    //             'options' => $question->options->map(function ($option) {
    //                 return (object) [
    //                     'id' => $option->id,
    //                     'option_text' => $option->option_text,
    //                     'correct_option' => $option->correct_option,
    //                 ];
    //             }),
    //         ];
    //     });

    //     return view('student.assignments.result', [
    //         'assignment' => $assignment,
    //         'questions' => $questionData,
    //         'total_score' => $result->total_score,
    //         'status' => $result->status,
    //     ]);
    // }
}
