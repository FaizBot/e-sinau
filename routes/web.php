<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Admin Controllers
use App\Http\Controllers\UserController as AdminUserController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\SchoolClassController as AdminSchoolClassController;

// Student Controllers
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\SchoolClassController as StudentSchoolClassController;

// Teacher Controllers
use App\Http\Controllers\Teacher\SchoolClassController as TeacherSchoolClassController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\TaskController as TeacherTaskController;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterialController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'indexadmin'])->name('register');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// route admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'indexadmin'])->name('admin.dashboard');

    // User Management
    Route::resource('admin/users', AdminUserController::class)->names('admin.users');

    // Classes Management
    Route::resource('admin/school_classes', AdminSchoolClassController::class)->names('admin.classes');
    Route::get('/admin/school_classes/ploting/{schoolClass}/plot', [AdminSchoolClassController::class, 'showPlotingForm'])->name('admin.classes.plot');
    Route::post('/admin/school_classes/ploting/{schoolClass}/plot', [AdminSchoolClassController::class, 'storePloting'])->name('admin.classes.plot.store');
    Route::delete('/admin/school_classes/ploting/{schoolClass}/plot/{student}', [AdminSchoolClassController::class, 'removePloting'])->name('admin.classes.plot.remove');

    // Course Management
    Route::resource('admin/classes.courses', AdminCourseController::class)->names('admin.classes.courses');

    // Tasks Management
    Route::resource('admin/classes.courses.tasks', AdminTaskController::class)->names('admin.courses.tasks');

    // Material Management
    Route::resource('admin/classes.courses.materials', AdminMaterialController::class)->names('admin.courses.materials');
    Route::patch('/admin/classes/{class}/courses/{course}/materials/{material}/publish', [AdminMaterialController::class, 'togglePublish'])->name('admin.materials.publish');
    Route::get('/admin/classes/{class}/courses/{course}/materials/{material}/preview', [AdminMaterialController::class, 'preview'])->name('admin.courses.materials.preview');

    // Assignment Management
    Route::get('/admin/classes/{class}/courses/{course}/assignments/create', [AdminAssignmentController::class, 'create'])->name('admin.assignment.create');
    Route::post('/admin/classes/{class}/courses/{course}/assignments', [AdminAssignmentController::class, 'store'])->name('admin.assignment.store');
    Route::delete('/admin/classes/{class}/courses/{course}/assignments/{assignment}/destroy', [AdminAssignmentController::class, 'destroy'])->name('admin.assignment.destroy');
    Route::patch('/admin/classes/{class}/courses/{course}/assignments/{assignment}/publish', [AdminAssignmentController::class, 'togglePublish'])->name('admin.assignment.publish');
    Route::get('/admin/classes/{class}/courses/{course}/assignments/{assignment}/show', [AdminAssignmentController::class, 'show'])->name('admin.assignment.show');
    Route::get('/admin/classes/{class}/courses/{course}/assignments/{assignment}/progress', [AdminAssignmentController::class, 'progress'])->name('admin.assignment.progress');
    Route::get('/admin/classes/{class}/courses/{course}/assignments/{assignment}/review/{nis}', [AdminAssignmentController::class, 'showAssignmentStudent'])->name('admin.assignment.review');
    Route::get('/admin/classes/{class}/courses/{course}/assignments/{assignment}/grade/{nis}', [AdminAssignmentController::class, 'correctAssignmentStudent'])->name('admin.assignment.grade');
    Route::put('/admin/classes/{class}/courses/{course}/assignments/{assignment}/grade/{nis}', [AdminAssignmentController::class, 'updateAssignmentStudent'])->name('admin.assignment.grade.update');
});

// route teacher
Route::middleware(['auth', 'role:teacher'])->group(function () {
    // Dashboard
    Route::get('/teacher/dashboard', [DashboardController::class, 'indexteacher'])->name('teacher.dashboard');

    // Classes Management
    Route::resource('teacher/school_classes', TeacherSchoolClassController::class)->names('teacher.classes');

    // // Course Management
    Route::resource('teacher/classes.courses', TeacherCourseController::class)->names('teacher.classes.courses');
    Route::get('/teacher/courses', [TeacherCourseController::class, 'teacher'])->name('teacher.courses.manage');

    // // Tasks Management
    Route::resource('teacher/classes.courses.tasks', TeacherTaskController::class)->names('teacher.courses.tasks');

    // // Material Management
    Route::resource('teacher/classes.courses.materials', TeacherMaterialController::class)->names('teacher.courses.materials');
    Route::patch('/teacher/classes/{class}/courses/{course}/materials/{material}/publish', [TeacherMaterialController::class, 'togglePublish'])->name('teacher.materials.publish');
    Route::get('/teacher/classes/{class}/courses/{course}/materials/{material}/preview', [TeacherMaterialController::class, 'preview'])->name('teacher.courses.materials.preview');

    // // Assignment Management
    Route::get('/teacher/classes/{class}/courses/{course}/assignments/create', [TeacherAssignmentController::class, 'create'])->name('teacher.assignment.create');
    Route::post('/teacher/classes/{class}/courses/{course}/assignments', [TeacherAssignmentController::class, 'store'])->name('teacher.assignment.store');
    Route::delete('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/destroy', [TeacherAssignmentController::class, 'destroy'])->name('teacher.assignment.destroy');
    Route::patch('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/publish', [TeacherAssignmentController::class, 'togglePublish'])->name('teacher.assignment.publish');
    Route::get('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/show', [TeacherAssignmentController::class, 'show'])->name('teacher.assignment.show');
    Route::get('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/progress', [TeacherAssignmentController::class, 'progress'])->name('teacher.assignment.progress');
    Route::get('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/review/{nis}', [TeacherAssignmentController::class, 'showAssignmentStudent'])->name('teacher.assignment.review');
    Route::get('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/grade/{nis}', [TeacherAssignmentController::class, 'correctAssignmentStudent'])->name('teacher.assignment.grade');
    Route::put('/teacher/classes/{class}/courses/{course}/assignments/{assignment}/grade/{nis}', [TeacherAssignmentController::class, 'updateAssignmentStudent'])->name('teacher.assignment.grade.update');
});

// route student
Route::middleware(['auth', 'role:student'])->group(function () {
    // Dashboard
    Route::get('/student/dashboard', [DashboardController::class, 'indexstudent'])->name('student.dashboard');

    // Class routes
    Route::resource('student/school_classes', StudentSchoolClassController::class)->names('student.classes');

    // Course routes
    Route::resource('student/classes.courses', StudentCourseController::class)->names('student.classes.courses');

    // Tasks routes
    Route::resource('student/classes.courses.tasks', StudentTaskController::class)->names('student.courses.tasks');

    // Materials Preview
    Route::get('/student/classes/{class}/courses/{course}/materials/{material}/preview', [StudentMaterialController::class, 'preview'])->name('student.courses.materials.preview');

    // Assignments
    Route::get('/student/classes/{class}/courses/{course}/assignments/{assignment}/show', [StudentAssignmentController::class, 'show'])->name('student.assignment.show');
    Route::post('/student/classes/{class}/courses/{course}/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submitAssignment'])->name('student.assignment.submit');
    Route::get('/student/classes/{class}/courses/{course}/assignments/{assignment}/result', [StudentAssignmentController::class, 'result'])->name('student.assignment.result');

    // Join class
    Route::get('student/join-class', [StudentSchoolClassController::class, 'showJoinForm'])->name('student.classes.join.form');
    Route::post('student/join-class', [StudentSchoolClassController::class, 'joinClass'])->name('student.classes.join');
});
