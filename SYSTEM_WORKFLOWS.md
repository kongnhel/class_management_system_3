# Class Management System - Complete Workflows & Data Flows Analysis

**Document Created:** May 15, 2026  
**System:** Educational Class Management System (Laravel-based)  
**Scope:** Comprehensive workflow and data flow documentation for system design, integration, and feature development

---

## 📋 TABLE OF CONTENTS

1. [System Overview](#system-overview)
2. [User Roles & Access Control](#user-roles--access-control)
3. [Student Enrollment Workflow](#student-enrollment-workflow)
4. [Course Management (Professor)](#course-management-professor)
5. [Attendance System](#attendance-system)
6. [Grading & Assessment System](#grading--assessment-system)
7. [Notification System](#notification-system)
8. [QR Login System](#qr-login-system)
9. [Database Schema Overview](#database-schema-overview)
10. [Key Data Relationships](#key-data-relationships)
11. [Special Features](#special-features)
12. [Route Structure](#route-structure)

---

## SYSTEM OVERVIEW

### Project Type
- **Framework:** Laravel 11+ with Breeze authentication
- **Frontend:** Blade templates + Tailwind CSS + Alpine.js + Livewire
- **Database:** SQLite (development), supports PostgreSQL/MySQL (production)
- **Deployment:** Vercel, Docker, Nixpacks compatible

### Core Technologies
- **Testing:** Pest PHP
- **Export:** DomPDF, PhpWord, Excel
- **Storage:** Cloudinary
- **Real-time:** Pusher, Laravel Echo
- **Chat/AI:** OpenAI API
- **Notifications:** Firebase, Telegram
- **Auth:** Email verification, QR code login, Google OAuth

### Current Roles (3-tier system)
```
Admin (System Management)
  ↓
Professor (Course Teaching & Grading)
  ↓
Student (Enrollment & Learning)
```

---

## USER ROLES & ACCESS CONTROL

### 1. **ADMIN Role**
**Access Level:** Full system management

**Key Responsibilities:**
- **User Management:** Create/edit/delete users (admin, professor, student)
- **Academic Structure:** Setup and manage:
  - Faculties (with dean assignment)
  - Departments (with head assignment)
  - Programs (study programs)
  - Courses (course definitions)
- **Course Offerings:** Create course offerings linking courses to programs
- **Student Enrollment:** Enroll students in courses
- **Classroom Management:** Setup rooms, schedules, locations
- **System Announcements:** Post institution-wide announcements
- **Exports:** User lists, course rosters

**Dashboard:**
- System overview statistics
- Quick links to management modules
- Recent activities

**Routes:** `/admin/*`

---

### 2. **PROFESSOR Role**
**Access Level:** Course and student management

**Key Responsibilities:**
- **Course Management:**
  - View assigned course offerings
  - View enrolled students
  - Access course schedules and timetables
- **Student Grading:**
  - Create and manage assignments
  - Create and manage exams
  - Create and manage quizzes
  - Enter student scores
  - View gradebook with rankings
  - Export grade reports (DOCX, CSV)
- **Attendance Tracking:**
  - Mark attendance manually
  - Generate QR codes for attendance
  - View attendance reports
  - Auto-close attendance (marks absents)
- **Communication:**
  - Create announcements
  - Send notifications to students
  - Send grade notifications via Telegram
- **Profile Management:**
  - Update professional profile
  - Department affiliation

**Dashboard:**
- My courses overview
- Upcoming classes
- Students to grade
- Attendance pending
- Notifications from admin

**Routes:** `/professor/*`

---

### 3. **STUDENT Role**
**Access Level:** Personal learning and enrollment

**Key Responsibilities:**
- **Course Management:**
  - View available courses (if self-enrollment enabled)
  - Enroll in courses
  - View enrolled courses
  - View course schedules
- **Grade Viewing:**
  - View final grades
  - View assessment scores (assignments, exams, quizzes)
  - View rank within class
  - View overall statistics
- **Assessments:**
  - Submit assignments (file upload)
  - Take exams
  - Take quizzes (online)
- **Attendance:**
  - Scan QR code for attendance
  - View attendance record
  - (If class leader) Submit class attendance
- **Communication:**
  - View announcements
  - Receive notifications
  - Mark notifications as read
- **Profile Management:**
  - Update personal profile
  - Link Telegram account

**Dashboard:**
- Current semester courses
- Upcoming deadlines
- Grades summary
- Notifications
- Attendance overview

**Routes:** `/student/*`

---

## STUDENT ENROLLMENT WORKFLOW

### Flow Diagram
```
STEP 1: ADMIN SETUP
├── Create Course (admin.courses.store)
├── Create Course Offering (admin.course-offerings.store)
│   ├── Assign to Program(s)
│   ├── Assign Professor as Lecturer
│   ├── Set Academic Year, Semester
│   ├── Set Capacity & Dates
│   └── Schedule Classes (day/time/room)
└── Enable/Disable Self-Enrollment

STEP 2: ENROLLMENT OPTIONS

Option A: MANUAL ENROLLMENT (Admin)
├── Admin visits: admin.enroll_student_form
├── Select Course Offering
├── Select Student
├── Click "Enroll Student"
└── StudentCourseEnrollment record created

Option B: SELF-ENROLLMENT (Student)
├── Student visits: student.available_courses
├── Browse available courses
├── Click "Enroll"
├── Validation:
│   ├── Student not already enrolled?
│   ├── Enrollment is open?
│   └── Capacity not exceeded?
└── StudentCourseEnrollment record created

STEP 3: POST-ENROLLMENT
├── Student status = "enrolled"
├── enrollment_date = today
├── final_grade = null
└── Student can now access:
    ├── Course schedule
    ├── Assignment list
    ├── Exam list
    ├── Quiz list
    ├── Attendance
    └── Grades view
```

### Key Tables Involved
| Table | Operation | Purpose |
|-------|-----------|---------|
| `student_course_enrollments` | INSERT | Record student-course link |
| `course_offerings` | SELECT | Get course capacity & status |
| `users` (students) | SELECT | Validate student exists |
| `student_program_enrollments` | SELECT | Check prerequisite enrollment |

### Validation Rules
```
1. Student must not already be enrolled in this offering
   - Check: StudentCourseEnrollment.where(student_user_id, course_offering_id)
   
2. Course offering must allow self-enrollment (if self-enrolling)
   - Check: CourseOffering.is_open_for_self_enrollment = true
   
3. Course offering must not be full
   - Check: count(enrolled) < capacity
   
4. Course offering must be active
   - Check: now() between start_date and end_date
```

### Enrollment Status Values
- `enrolled` - Currently enrolled
- `completed` - Course completed (after semester ends)
- `dropped` - Student dropped course

### Example Endpoint Calls
```
POST /admin/perform-enrollment
Body: {
  student_id: 123,
  course_offering_id: 456,
  enrollment_date: "2026-05-15"
}

POST /student/enroll-self
Body: {
  program_id: 789,
  course_offering_id: 456
}
```

---

## COURSE MANAGEMENT (PROFESSOR)

### Professor Dashboard Overview
```
Professor logs in (role = 'professor')
                    ↓
Dashboard displays:
├── My Course Offerings (courses assigned to this professor)
├── All Students (search/filter all system students)
├── All Course Offerings (view all offerings, filter by semester/program)
├── My Schedule (timetable for this professor)
└── Quick Access Links:
    ├── Grades to Enter
    ├── Attendance to Mark
    ├── Assignments to Create
    └── Exams to Create
```

### Course Offering Components

#### 1. **Basic Information**
```
CourseOffering Record:
├── course_id → Links to Course definition
├── lecturer_user_id → Professor's User ID
├── program_id → Target program
├── academic_year → e.g., "2025-2026"
├── semester → "Fall", "Spring", "Summer"
├── section → "A", "B", "C" (if multiple sections)
├── capacity → Max enrolled students
├── room_number → Physical classroom
├── is_open_for_self_enrollment → boolean
├── start_date → Course begins
├── end_date → Course ends
├── created_at, updated_at
└── deleted_at → Soft delete support
```

#### 2. **Schedule Management**
```
Schedule Records (one-to-many from CourseOffering):
├── day_of_week → "Monday", "Tuesday", etc.
├── start_time → "08:00" (24-hour format)
├── end_time → "09:30"
├── room_id → Foreign key to Room
└── course_offering_id → Foreign key to CourseOffering

Validation: No conflicts with:
- Same lecturer at same time
- Same room at same time
```

#### 3. **Enrolled Students**
```
StudentCourseEnrollment Records:
├── student_user_id → Link to User (student)
├── course_offering_id → Link to CourseOffering
├── enrollment_date → When enrolled
├── final_grade → Letter grade (A, B+, B, C+, C, F)
├── attendance_score_manual → Override attendance score
├── is_class_leader → Boolean (can submit class attendance)
└── status → "enrolled", "completed", "dropped"
```

### Professor Workflows

#### **A. View Course Details**
```
GET /professor/my-course-offerings
                ↓
Display list of courses where lecturer_user_id = Auth::user()->id
                ↓
Professor clicks course to view:
- Enrolled student list (with profiles)
- Schedule
- Created assessments
- Attendance record
- Grades summary
```

#### **B. View Student List**
```
GET /professor/course-offerings/{courseOffering}/students
                ↓
Retrieve: CourseOffering.students (via students() relationship)
                ↓
Display:
├── Student name & ID
├── Student program & generation
├── Enrollment date
├── Current grade (if exists)
├── Attendance (present/absent count)
├── Class leader status
└── Actions: 
    ├── View profile
    ├── Toggle class leader
    └── View individual grades
```

#### **C. Create Assessment (Assignment/Exam/Quiz)**
```
POST /professor/course-offerings/{offering_id}/assessments
Body: {
  assessment_type: "assignment", // or "exam", "quiz"
  title_en: "Assignment 1",
  title_km: "កិច្ចការ ១",
  max_score: 20,
  assessment_date: "2026-06-01",
  grading_category_id: null
}
                ↓
CREATE:
- Assignment record (with course_offering_id, max_score, due_date)
  OR
- Exam record (with course_offering_id, max_score, exam_date)
  OR
- Quiz record (with course_offering_id, max_score, quiz_date)
                ↓
Notify students in course
```

#### **D. Grade Entry & Calculation**
```
GET /professor/course-offering/{offering_id}/grades
                ↓
Retrieve:
1. All students in course offering
2. All assessments (assignments, exams, quizzes)
3. All grades (from ExamResult table)
4. Calculate for each student:
   ├── Attendance score (auto from attendance records)
   ├── Assignment scores (sum from ExamResult where assessment_type='assignment')
   ├── Exam scores (sum from ExamResult where assessment_type='exam')
   ├── Quiz scores (sum from ExamResult where assessment_type='quiz')
   ├── Total score = attendance + assignment + exam + quiz
   └── Letter grade assignment
                ↓
Display gradebook:
├── Student list (sorted by total score)
├── Score for each assessment
├── Total score
├── Letter grade (A, B+, B, C+, C, F)
└── Rank within class
                ↓
POST /professor/grades/{courseOffering}/store
Body: Array of student scores for each assessment
                ↓
For each score:
  CREATE/UPDATE ExamResult(
    assessment_id: 123,
    assessment_type: "assignment",
    student_user_id: 456,
    score_obtained: 18
  )
                ↓
UPDATE StudentCourseEnrollment.final_grade = "B+"
```

#### **E. Export Gradebook**
```
GET /professor/course-offerings/{offering_id}/export-gradebook
                ↓
Generate DOCX file via PhpWord:
├── Header: Course name, semester, professor
├── Date generated
└── Table:
    ├── Student name & ID
    ├── All assessments (columns)
    ├── Attendance score
    ├── Total score
    ├── Letter grade
    └── Rank
                ↓
Download file: "Gradebook_CourseCode_Semester.docx"
```

### Professor Permissions Enforced
```
Middleware: 'auth', 'role:professor'

Can only:
✓ Access own course offerings (where lecturer_user_id = Auth::user()->id)
✓ View only enrolled students (via StudentCourseEnrollment)
✓ Modify grades for own courses
✓ Modify attendance for own courses
✓ Create announcements for own courses

Cannot:
✗ View other professor's courses
✗ Modify other professor's grades
✗ Create course offerings (admin only)
✗ Modify attendance for other courses
```

---

## ATTENDANCE SYSTEM

### Architecture Overview
```
TWO MODES:
1. QR-Based (Automated) - Most common
2. Manual Entry (Professor) - Fallback
3. Auto-Closure - Prevents tampering

SINGLE SCORING SYSTEM:
- All modes feed into same scoring calculation
- 15% weight in final grade
- Formula: max_score - deductions
```

### Mode 1: QR-Based Attendance (Automated)

#### Flow Diagram
```
AT START OF CLASS:

Professor's Dashboard
        ↓
[Click] "Generate QR Code" for CourseOffering
        ↓
CREATE AttendanceQrToken:
├── token_code: UUID (e.g., 550e8400-e29b-41d4-a716-446655440000)
├── course_offering_id: 123
├── expires_at: now() + 10 minutes
└── created_at: now()
        ↓
DISPLAY QR Code (png/svg) on Professor's Screen
        ↓
JavaScript on professor's page stores token in session

DURING CLASS:

Student sees QR on professor's screen
        ↓
Student opens mobile app
        ↓
Mobile app camera → QR scanner
        ↓
[Scan] QR Code
        ↓
Mobile sends API request:
POST /qr-authorize
Headers: Authorization: Bearer {mobile_auth_token}
Body: { token: "550e8400-e29b-41d4-a716-446655440000" }
        ↓
SERVER VALIDATION:
1. Is user authenticated? (on mobile)
   └─ No? → Error: "Please login first"
2. Does token exist in cache?
   └─ No? → Error: "QR Code invalid or expired"
3. Is token not expired?
   └─ Expired? → Error: "QR Code expired, ask professor to generate new one"
4. Is user enrolled in course offering?
   └─ No? → Error: "You are not enrolled in this course"
5. Has user already scanned today?
   └─ Yes? → Error: "You already scanned today"
        ↓
IF ALL VALIDATIONS PASS:
├── CREATE AttendanceRecord:
│   ├── student_user_id: user_id
│   ├── course_offering_id: 123
│   ├── date: today
│   ├── status: "present"
│   ├── remarks: "QR Scan"
│   └── created_at: now()
├── DELETE token from cache (prevent replay)
└── RESPONSE to mobile: { success: true, message: "Attendance recorded" }

PROFESSOR SEES ON SCREEN:
- Real-time count: "15 students scanned"
- List of scanned students updates
```

### Mode 2: Manual Attendance (Professor)

#### Flow Diagram
```
GET /professor/course-offerings/{courseOffering}/attendance
        ↓
DISPLAY attendance form with:
├── Course offering details
├── Date selector (pick which date to mark)
├── List of all enrolled students
└── Status selector for each student:
    ├── Present
    ├── Absent
    ├── Late
    └── Permission (excused absence)
        ↓
Professor marks students:
[✓] Student 1 - Present
[✗] Student 2 - Absent
[⏱] Student 3 - Late
[📋] Student 4 - Permission
        ↓
POST /professor/course-offerings/{courseOffering}/attendance
Body: Array of attendance records
[
  { student_user_id: 1, status: "present", date: "2026-05-15" },
  { student_user_id: 2, status: "absent", date: "2026-05-15" },
  ...
]
        ↓
CREATE AttendanceRecord for each entry:
INSERT INTO attendances (student_user_id, course_offering_id, date, status, remarks)
VALUES (1, 123, "2026-05-15", "present", "Manual entry")
```

### Mode 3: Auto-Closure

#### Flow Diagram
```
END OF CLASS / End of Day:

Professor clicks "Close Attendance" for CourseOffering
        ↓
QUERY 1: Get all enrolled students
SELECT student_user_id FROM student_course_enrollments 
WHERE course_offering_id = 123
Result: [1, 2, 3, 4, 5, 6, 7, 8]
        ↓
QUERY 2: Get students who scanned/marked present today
SELECT DISTINCT student_user_id FROM attendances 
WHERE course_offering_id = 123 AND date = today
Result: [1, 3, 5, 8]
        ↓
CALCULATION: Absent students = enrolled - present
= [1,2,3,4,5,6,7,8] - [1,3,5,8] = [2, 4, 6, 7]
        ↓
FOR EACH ABSENT STUDENT:
INSERT INTO attendances (student_user_id, course_offering_id, date, status, remarks)
VALUES (2, 123, today, "absent", "Auto-generated (No Scan)")
        ↓
ALL STUDENTS NOW HAVE RECORD FOR TODAY
✓ Both scanned and manual entry
✓ No one can be "missing" from record
✓ Prevents tampering (can't mark late students absent later)
```

### Attendance Scoring System

#### Algorithm
```
getAttendanceScoreByCourse(course_offering_id):

STEP 1: Get all attendance records for this student in this course
SELECT * FROM attendances 
WHERE student_user_id = X AND course_offering_id = Y

STEP 2: Count by status
absent_count = count(status = 'absent')
permission_count = count(status = 'permission')

STEP 3: Calculate deductions
deduction = (floor(absent_count / 2)) + (floor(permission_count / 4))
// 2 absences = -1 point
// 4 permissions = -1 point

STEP 4: Calculate final score
max_score = 15
final_score = max_score - deduction
final_score = max(0, final_score)  // Can't be negative

EXAMPLE:
- 0 absences, 0 permissions → 15 - 0 = 15/15
- 2 absences, 0 permissions → 15 - 1 = 14/15
- 4 absences, 0 permissions → 15 - 2 = 13/15
- 2 absences, 4 permissions → 15 - (1+1) = 13/15
```

#### Manual Override
```
StudentCourseEnrollment.attendance_score_manual field:

getAttendanceScoreByCourse():
  if enrollment.attendance_score_manual IS NOT NULL:
    return enrollment.attendance_score_manual  // Professor manually set
  else:
    return auto_calculated_score  // Use formula above

Professor can:
1. View auto-calculated score
2. Override with manual score
3. Save override to StudentCourseEnrollment.attendance_score_manual
```

### Attendance Status Types
| Status | Meaning | Deduction | Used By |
|--------|---------|-----------|---------|
| present | Attended class | None | QR scan or manual |
| absent | Did not attend | -0.5 per 2 | QR, manual, auto-close |
| late | Attended but late | None (or custom) | Manual entry |
| permission | Excused absence | -0.25 per 4 | Manual entry |

### Key Tables & Relationships
```
AttendanceQrToken (temporary):
├── token_code: UUID
├── course_offering_id: FK
├── expires_at: timestamp
└── created_at: timestamp

AttendanceRecord (permanent):
├── id: PK
├── student_user_id: FK → User (student)
├── course_offering_id: FK → CourseOffering
├── date: date
├── status: enum('present','absent','late','permission')
├── remarks: text (e.g., "QR Scan", "Auto-generated")
├── created_at, updated_at
└── Indexes: (student_user_id, course_offering_id, date) - composite unique
```

### Attendance Validation Rules
```
When scanning QR:
✓ User is authenticated on mobile
✓ Token exists and not expired
✓ Student is enrolled in course offering
✓ Not already scanned today for this course

When manually marking:
✓ Professor owns this course offering
✓ Date is within course offering dates
✓ Student is enrolled

When auto-closing:
✓ User clicks "close"
✓ Course offering exists
✓ Today is within course offering dates
```

### Attendance Reports
```
GET /professor/course-offerings/{courseOffering}/attendance-report
Displays:
├── Each student's attendance record
├── Attendance percentage
├── Deductions
├── Current score (out of 15)
└── Export to DOCX

GET /student/my-attendance
Student sees:
├── Each course's attendance
├── Count: present/absent/late/permission
├── Current score (out of 15)
└── Overall attendance summary
```

---

## GRADING & ASSESSMENT SYSTEM

### Architecture Overview

#### Grade Components (Default Weights)
```
Total Grade = 100 points

1. Attendance: 15%
   - Calculated from AttendanceRecord
   - 0-15 points based on absences/permissions
   - Can be manually overridden

2. Assignment: 20%
   - Created by professor for each course offering
   - Students submit files
   - Professor grades each submission

3. Quiz: 10%
   - Online questions with answers
   - Can be auto-graded (multiple choice)
   - Or manual graded (essays)

4. Midterm Exam: 15%
   - Identified by max_score ≤ 15

5. Final Exam: 50%
   - Largest exam of the course
```

### Grade Calculation Flow

#### Step 1: Assessment Creation
```
Professor creates assessment (via manageGrades or createAssessmentForm):

For ASSIGNMENT:
POST /professor/course-offerings/{id}/assignments
Body: {
  title_en: "Assignment 1",
  title_km: "កិច្ចការ ១",
  max_score: 20,
  due_date: "2026-06-01",
  grading_category_id: 1
}
        ↓
CREATE Assignment record:
├── course_offering_id: FK
├── grading_category_id: FK (optional)
├── title_km, title_en
├── max_score: 20
└── due_date: date

For EXAM:
POST /professor/course-offerings/{id}/exams
Body: {
  title_en: "Final Exam",
  title_km: "ការប្រឡងចុងឆមាស",
  max_score: 50,  // Identified as Final (large value)
  exam_date: "2026-07-01",
  duration_minutes: 120
}
        ↓
CREATE Exam record:
├── course_offering_id: FK
├── title_km, title_en
├── max_score: 50
├── exam_date: date
└── duration_minutes

For QUIZ:
POST /professor/course-offerings/{id}/assessments
Body: {
  assessment_type: "quiz",
  title_en: "Quiz 1",
  title_km: "ត្រាប់មតិ ១",
  max_score: 10,
  assessment_date: "2026-06-15"
}
        ↓
CREATE Quiz record:
├── course_offering_id: FK
├── title_km, title_en
├── max_score: 10
└── quiz_date: date
```

#### Step 2: Student Completion

```
For ASSIGNMENT:
1. Student views assignment in My Assignments
2. Student clicks "Submit" → File upload dialog
3. Student selects file (PDF, DOCX, ZIP, etc.)
4. System uploads to Cloudinary
5. CREATE Submission record:
   ├── assignment_id: FK
   ├── student_user_id: FK
   ├── submission_date: now()
   ├── file_path: "cloudinary://url"
   └── grade_received: NULL (not graded yet)

For EXAM:
1. Professor administers exam (paper or system)
2. Student takes exam

For QUIZ:
1. Student takes quiz online
2. CREATE StudentQuizResponse for each answer:
   ├── quiz_id: FK
   ├── student_user_id: FK
   ├── selected_answer: text
   └── timestamp
```

#### Step 3: Grade Entry

```
Professor views gradebook:
GET /professor/course-offerings/{offering_id}/grades
        ↓
Display form with:
- Student list (sorted by name/ID)
- Column for each assessment
- Attendance score (auto-calculated)
- Total score (auto-calculated)
- Letter grade (auto-assigned)
        ↓
Professor enters scores:

For ASSIGNMENT:
- Professor enters score out of 20
- OR clicks submission to review before grading
- Score validated: 0 ≤ score ≤ max_score

For EXAM:
- Professor enters score out of max_score
- If multiple exams, auto-categorizes:
  - max_score ≤ 15 → Midterm
  - max_score > 15 → Final

For QUIZ:
- Auto-graded if multiple choice
- Manual if essays
        ↓
POST /professor/grades/store
Body: [
  { student_id: 1, assessment_id: 10, assessment_type: "assignment", score: 18 },
  { student_id: 1, assessment_id: 20, assessment_type: "exam", score: 45 },
  ...
]
        ↓
CREATE/UPDATE ExamResult records:
INSERT INTO exam_results (
  assessment_id: 10,
  assessment_type: "assignment",
  student_user_id: 1,
  score_obtained: 18,
  recorded_at: now()
)
```

#### Step 4: Final Grade Calculation

```
manageGrades(offering_id):

FOR EACH STUDENT in course offering:
  
  1. Get Attendance Score
     attendance_score = getAttendanceScoreByCourse(offering_id)
     // Result: 0-15
  
  2. Collect All Assessment Scores
     assignments = ExamResult.where(assessment_type='assignment')
     exams = ExamResult.where(assessment_type='exam')
     quizzes = ExamResult.where(assessment_type='quiz')
     
  3. Sum Each Category
     assignment_total = assignments.sum('score_obtained')
     exam_total = exams.sum('score_obtained')
     quiz_total = quizzes.sum('score_obtained')
  
  4. Calculate Total Score
     total_score = attendance_score 
                  + assignment_total 
                  + exam_total 
                  + quiz_total
  
  5. Assign Letter Grade
     if total_score >= 85:
       letter_grade = "A"
     elseif total_score >= 80:
       letter_grade = "B+"
     elseif total_score >= 70:
       letter_grade = "B"
     elseif total_score >= 65:
       letter_grade = "C+"
     elseif total_score >= 50:
       letter_grade = "C"
     else:
       letter_grade = "F"
  
  6. Check Failure Conditions
     is_failed = false
     if midterm_score < 9 (out of 15):
       is_failed = true  // Auto F if fails midterm
     if final_exam < 24 (out of 50):
       is_failed = true  // Auto F if fails final
     if assignment_total < 9 (out of 20):
       is_failed = true  // Auto F if fails assignment
     if attendance_score < 9 (out of 15):
       is_failed = true  // Auto F if attendance too low
     
     if is_failed:
       letter_grade = "F"
  
  7. Calculate Rank
     Sort all students by total_score DESC
     rank = position in sorted list
     // Student 1 with highest score → rank 1
     // Student 2 with 2nd highest → rank 2
     // etc.
  
  8. Store Final Grade
     UPDATE student_course_enrollments 
     SET final_grade = "B"
     WHERE student_user_id = X AND course_offering_id = Y
```

### Grade Viewing (Student Perspective)

```
GET /student/my-grades
        ↓
Student's myGrades controller:

1. Retrieve all ExamResult records for this student
   SELECT * FROM exam_results 
   WHERE student_user_id = Auth::user()->id

2. For each result:
   a. Find the assessment (Assignment/Exam/Quiz)
   b. Get course info from assessment
   c. Get course offering from assessment
   d. Calculate percentage: (score / max_score) * 100

3. Group results by course_offering_id:
   
   Course 1 (Python Programming):
   ├── Attendance: 14/15
   ├── Assignment 1: 18/20 (90%)
   ├── Quiz 1: 8/10 (80%)
   ├── Midterm: 13/15 (86%)
   ├── Final: 44/50 (88%)
   ├── Total: 97/100
   ├── Grade: A
   ├── Rank: 2nd out of 25 students
   └── Status: Not Failed

4. Calculate Overall Stats:
   ├── Average score across all courses
   ├── Total points earned
   ├── Overall rank (across all courses)
   └── GPA equivalent

5. Display in view:
   - Course-by-course breakdown
   - Component scores
   - Overall statistics
   - Download transcript option
```

### Letter Grade Scale
```
Score Range → Letter Grade
85-100      → A
80-84       → B+
70-79       → B
65-69       → C+
50-64       → C
0-49        → F (Fail)

Special Case: Failure Conditions
- Midterm < 9/15 → Automatic F
- Final < 24/50 → Automatic F
- Assignment < 9/20 → Automatic F
- Attendance < 9/15 → Automatic F
```

### Key Tables Involved
```
ExamResult (Core grades table):
├── id: PK
├── assessment_id: FK (Assignment/Exam/Quiz ID)
├── assessment_type: enum('assignment','exam','quiz')
├── student_user_id: FK → User
├── score_obtained: decimal (points earned)
├── notes: text (optional)
├── recorded_at: timestamp
└── Indexes: (assessment_id, assessment_type, student_user_id) - unique

StudentCourseEnrollment:
├── student_user_id, course_offering_id: PK composite
├── final_grade: string (Letter grade stored here)
├── attendance_score_manual: decimal (override)
└── is_class_leader: boolean

Submission (Assignment submissions):
├── assignment_id: FK
├── student_user_id: FK
├── submission_date: timestamp
├── file_path: string (Cloudinary URL)
├── grade_received: decimal (score out of max)
└── feedback: text (professor comments)
```

### Export Features

#### 1. **DOCX Gradebook Export**
```
GET /professor/course-offerings/{offering_id}/export-gradebook
        ↓
Generate Word document via PhpWord:

Header:
- Institution name
- Course: [course name]
- Semester: [academic year] [semester]
- Professor: [name]
- Date: [generated date]

Table:
┌──────┬───────────────┬─────────────────┬─────────┬──────────┐
│ Rank │ Student Name  │ Assessments (×) │ Total   │ Grade    │
├──────┼───────────────┼─────────────────┼─────────┼──────────┤
│ 1    │ Sokha         │ 18/20, 13/15... │ 97/100  │ A        │
│ 2    │ Sophea        │ 17/20, 14/15... │ 94/100  │ A        │
└──────┴───────────────┴─────────────────┴─────────┴──────────┘

Footer:
- Total students: 25
- Average score: 78.2
```

#### 2. **CSV Bulk Import/Export**
```
GET /assessment/{id}/export-csv
        ↓
Export: CSV with columns:
student_id, student_name, score, max_score, percentage

POST /assessment/{id}/import-csv
        ↓
Upload CSV → System parses → Update scores
Allows bulk grade updates from external source
```

#### 3. **Student List Export**
```
GET /professor/course-offering/{offering_id}/export
        ↓
Generate DOCX:
- Student name, ID, email
- Program & generation
- Enrollment date
- Current status
```

---

## NOTIFICATION SYSTEM

### Architecture Overview

```
TWO CHANNELS:
1. Database Notifications (Real-time via Pusher)
2. Telegram Notifications (Grade announcements)

THREE SOURCES:
1. Announcements (broadcast to course/all users)
2. Grade Notifications (when grades posted)
3. Assignment Notifications (new assignment created)
4. Custom Messages (professor messages)
```

### Database Notifications

#### Flow Diagram
```
Professor creates notification:

GET /professor/notifications/create
        ↓
Display form:
- Recipient selection:
  ├── All course students
  ├── Specific students
  └── All users
- Message content
- Channel: Database / Telegram / Email
        ↓
POST /professor/notifications/store
Body: {
  recipient_type: "course_students",
  course_offering_id: 123,
  message: "Grades have been posted",
  channel: "database"
}
        ↓
DETERMINE RECIPIENTS:

if recipient_type == "course_students":
  recipients = StudentCourseEnrollment
    .where('course_offering_id', 123)
    .pluck('student_user_id')
    // Result: [1, 2, 3, 4, 5]

else if recipient_type == "all":
  recipients = User.where('role', 'student').pluck('id')
  // Result: all students
        ↓
FOR EACH RECIPIENT:
  CREATE Notification record:
  INSERT INTO notifications (
    id: UUID,
    type: 'grade_posted',
    notifiable_type: 'App\Models\User',
    notifiable_id: student_user_id,
    data: JSON {
      message: "Grades have been posted",
      course_id: 123,
      url: "/student/my-grades"
    },
    read_at: NULL,
    created_at: now()
  )
        ↓
BROADCAST EVENT (via Pusher):
Event: 'notification.created'
Channel: private-user.{student_user_id}
Data: {notification_id, message, url}
        ↓
STUDENT'S BROWSER (Real-time):
1. Receives event from Pusher
2. Shows "You have 1 new notification"
3. Updates notification bell icon
4. Adds to notification feed
```

#### Student Viewing Notifications

```
GET /student/notifications
        ↓
Display:
- List of all notifications (paginated)
- Unread count at top
- Each notification:
  ├── Message
  ├── Timestamp (relative: "5 minutes ago")
  ├── Read status (read/unread)
  └── Actions:
      ├── Mark as read (if unread)
      └── Delete

When student clicks notification:
        ↓
PATCH /student/notifications/{id}/read
        ↓
UPDATE notifications
SET read_at = now()
WHERE id = {notification_id}
        ↓
Display notification details
        ↓
Redirect to relevant page (e.g., /student/my-grades)
```

### Announcements (Special Notification Type)

```
CREATE Announcement:

POST /admin/announcements/store (or /professor/announcements/store)
Body: {
  title: "Important: Grade Posting Delayed",
  content: "Grades will be posted by Friday",
  course_offering_id: 123 (if course-specific)
}
        ↓
CREATE Announcement record:
├── id: PK
├── poster_user_id: FK (Admin or Professor)
├── course_offering_id: FK (NULL if institution-wide)
├── title: string
├── content: text
├── created_at, updated_at
└── deleted_at: soft delete

If course-specific:
  Recipients = all students enrolled in course
  
If institution-wide:
  Recipients = all users
        ↓
Display in student notifications view
Track read status in announcement_reads table
```

### Telegram Integration

#### Setup
```
Prerequisites:
1. Professor links Telegram account
2. Student links Telegram account to profile
3. Telegram bot created and configured
4. Webhook registered at /professor/telegram/webhook

Both need:
- telegram_user_id: ID of user in Telegram
- telegram_chat_id: Chat ID for DM
```

#### Grade Notification Flow
```
Professor posts grades:
        ↓
POST /professor/send-grade-telegram/{enrollment_id}
        ↓
RETRIEVE student info:
- enrollment_id → StudentCourseEnrollment
- Get student → User
- Get telegram_chat_id from UserProfile
- Get course info
- Get final_grade from enrollment
        ↓
CONSTRUCT MESSAGE:
"📊 Grade Posted: [Course Name]
You received: [Final Grade]
Score: [Score]/100
Rank: [Rank]/[Total Students]
Check your grades: [Link]"
        ↓
SEND VIA TELEGRAM BOT:
POST https://api.telegram.org/botXXXXXXXXXXX/sendMessage
Body: {
  chat_id: "123456789",
  text: message,
  parse_mode: "HTML"
}
        ↓
RESPONSE: Message delivered to student's Telegram
```

#### Bulk Send
```
POST /professor/course-offering/{id}/send-all-telegram

For each StudentCourseEnrollment in course_offering:
  if student has telegram_chat_id:
    - Construct grade message
    - Send to Telegram
  else:
    - Log warning: "Student {name} has no Telegram"
        ↓
Response to professor: "Sent to 23/25 students"
```

### Notification Storage

```
notifications table (Laravel's default):
├── id: UUID (primary key)
├── type: string (class name or custom type)
├── notifiable_type: string ('App\Models\User')
├── notifiable_id: integer (user_id)
├── data: JSON {
│   message: "string",
│   course_id: "integer",
│   url: "string"
│ }
├── read_at: timestamp (NULL = unread)
├── created_at, updated_at: timestamps
└── Index on (notifiable_type, notifiable_id, read_at)

Query unread for user:
SELECT * FROM notifications
WHERE notifiable_type = 'App\Models\User'
  AND notifiable_id = {user_id}
  AND read_at IS NULL
```

### Notification Types
| Type | Source | Recipients | Channel |
|------|--------|-----------|---------|
| grade_posted | Professor | Course students | Database + Telegram |
| assignment_created | Professor | Course students | Database |
| announcement | Admin/Professor | Course or all | Database |
| custom_message | Professor | Selected students | Database |
| exam_reminder | System | Course students | Database |

---

## QR LOGIN SYSTEM

### Purpose
Allow students on mobile to authorize desktop/web login via QR code scan

### Security Features
- Token expires after 5 minutes
- Token deleted after first use (prevents replay)
- Requires mobile authentication first
- Token stored only in cache (not database)

### Component Architecture

#### Desktop/Web Side
```
Browser: http://example.com/login

1. User visits /login
   GET /login → QrLoginController::showQrForm()

2. Server generates token:
   $token = Str::uuid()  // e.g., 550e8400-e29b-41d4-a716-446655440000
   Cache::put('login_token_' . $token, true, now()->addMinutes(5))
   // Store in cache with 5-min expiration

3. Server generates QR code from token:
   $qrCode = QrCode::size(250)
     ->color(16, 185, 129)
     ->margin(1)
     ->generate($token)
   // Creates PNG/SVG of QR code

4. Display on page:
   - Large QR code image
   - Refresh button (generate new QR)
   - Instructions: "Scan with mobile app"
   - Fallback login link

5. JavaScript polling:
   Every 1 second:
   - Check if 'authorized_user_' . $token in cache
   - If yes:
     - Login user
     - Redirect to dashboard
```

#### Mobile/App Side
```
Mobile App (Already logged in):

1. User taps "Scan QR Code"
2. Camera opens → QR scanner
3. User scans desktop's QR code
4. App extracts token from QR code
5. App sends API request:

   POST /qr-authorize
   Headers: {
     Authorization: Bearer {mobile_user_token},
     Content-Type: application/json
   }
   Body: {
     token: "550e8400-e29b-41d4-a716-446655440000"
   }

6. Server validation (in QrLoginController::handleScan):

   a) Verify mobile user is authenticated:
      if (!Auth::check()):
        return error(401, "Please login on mobile first")
   
   b) Verify token exists:
      if (!Cache::has('login_token_' . $token)):
        return error(400, "Invalid QR code")
   
   c) Delete token (prevent replay):
      Cache::forget('login_token_' . $token)
   
   d) Store authorization:
      Cache::put('authorized_user_' . $token, 
                 Auth::user()->id, 
                 now()->addMinutes(2))
   
   e) Broadcast event:
      broadcast(new QrLoginSuccessful($token, Auth::user()->id))
   
   f) Return success:
      return response()->json(['status' => 'success'])

7. Mobile receives success response
```

#### Desktop Completes Login

```
Desktop JavaScript detects authorization:

Polling detects 'authorized_user_' + $token in cache

OR

Listening to Pusher event 'QrLoginSuccessful':

When event received:
1. Redirect to finalize endpoint:
   GET /qr-login/finalize/{token}

2. Server processes (QrLoginController::finalizeLogin):
   
   a) Retrieve stored user_id:
      $userId = Cache::pull('authorized_user_' . $token)
      // Cache::pull deletes the key
   
   b) Verify user_id exists:
      if (!$userId):
        redirect to /login with error
   
   c) Login user:
      Auth::loginUsingId($userId)
      // Desktop session created
   
   d) Redirect to dashboard:
      return redirect()->intended(route('dashboard'))

3. Desktop user is now logged in
```

### Sequence Diagram (Text)
```
DESKTOP                     SERVER                    MOBILE
  │                          │                          │
  │─────────────────────────>│                          │
  │  GET /login              │                          │
  │                          │                          │
  │<─────────────────────────│                          │
  │  QR Code + Token         │                          │
  │                          │                          │
  │ Display QR               │                          │
  │ [JavaScript polling]     │                          │
  │                          │  User scans QR           │
  │                          │<─────────────────────────│
  │                          │  POST /qr-authorize      │
  │                          │  + token                 │
  │                          │                          │
  │                          │  Validate & store auth   │
  │                          │  Broadcast event         │
  │                          │                          │
  │                          │  Success response        │
  │                          │──────────────────────────>
  │                          │                          │
  │  Polling detects auth    │                          │
  │  OR receives broadcast   │                          │
  │                          │                          │
  │─────────────────────────>│                          │
  │  GET /qr-login/finalize  │                          │
  │                          │                          │
  │  Finalize & login        │                          │
  │<─────────────────────────│                          │
  │  Redirect to /dashboard  │                          │
  │                          │                          │
```

### Error Scenarios

```
SCENARIO: Mobile not logged in
Mobile: POST /qr-authorize (no auth token)
Server: Return 401 "Unauthorized - Please login first"
Result: Mobile user sees error, must login first

SCENARIO: QR code expired
Mobile: POST /qr-authorize (5+ mins after QR generated)
Server: Token not in cache
Result: Error "QR Code expired, ask professor for new one"
        Desktop polls, sees no authorization, shows refresh button

SCENARIO: Wrong QR code scanned
Mobile: POST /qr-authorize (wrong token)
Server: Token not found
Result: Error "Invalid QR code"

SCENARIO: Replay attack
Mobile 1: Scans QR successfully → Token deleted
Mobile 2: Tries same QR code → Token not found
Result: Error "Invalid QR code"
Security: ✓ Prevented
```

### Configuration
```
Timeout values:
- QR code generation timeout: 5 minutes
- Authorization cache timeout: 2 minutes
- Mobile cache key: 'authorized_user_' . {token}
- Desktop cache key: 'login_token_' . {token}

Fallback:
- If Pusher not available, desktop polls every 1 second
- Manual refresh QR button available
- Traditional login link always available
```

---

## DATABASE SCHEMA OVERVIEW

### Core Tables Summary

```
AUTHENTICATION & USERS:
├── users (id, name, email, role, password, email_verified_at, ...)
├── user_profiles (user_id, phone, telegram_id, avatar, bio, ...)
├── student_profiles (user_id, student_id_code, generation, address, ...)
└── professor_profiles (user_id, qualifications, department_affiliation, ...)

ACADEMIC STRUCTURE:
├── faculties (id, name_km, name_en, dean_user_id, ...)
├── departments (id, name_km, name_en, head_user_id, faculty_id, ...)
├── programs (id, name_km, name_en, credits_required, department_id, ...)
└── courses (id, title_km, title_en, credits, description, ...)

COURSES & OFFERINGS:
├── course_offerings (id, course_id, program_id, lecturer_user_id, ...)
├── course_offering_program (course_offering_id, program_id, generation, ...)
├── schedules (id, course_offering_id, day_of_week, start_time, end_time, ...)
└── rooms (id, room_number, capacity, location, ...)

ENROLLMENT:
├── student_program_enrollments (student_user_id, program_id, enrollment_date, ...)
└── student_course_enrollments (student_user_id, course_offering_id, final_grade, ...)

ATTENDANCE:
├── attendance_records (id, student_user_id, course_offering_id, date, status, ...)
└── attendance_qr_tokens (token_code, course_offering_id, expires_at, ...)

ASSESSMENTS & GRADING:
├── grading_categories (id, course_id, name_km, name_en, weight_percentage, ...)
├── assignments (id, course_offering_id, title_km, title_en, max_score, ...)
├── submissions (id, assignment_id, student_user_id, file_path, grade_received, ...)
├── exams (id, course_offering_id, title_km, title_en, exam_date, max_score, ...)
├── exam_results (id, assessment_id, assessment_type, student_user_id, score_obtained, ...)
├── quizzes (id, course_offering_id, title_km, title_en, max_score, ...)
├── quiz_results (id, quiz_id, student_user_id, ...)
└── student_quiz_responses (id, quiz_id, student_user_id, selected_answer, ...)

COMMUNICATION:
├── announcements (id, poster_user_id, course_offering_id, title, content, ...)
├── announcement_reads (user_id, announcement_id, read_at, ...)
├── notifications (id, type, notifiable_type, notifiable_id, data, read_at, ...)
└── chat_messages (id, user_id, ai_response, timestamp, ...)
```

### Critical Relationships

```
User ← Center of Most Relationships →
  
  1:1 → UserProfile (extended info)
  1:1 → StudentProfile (if student)
  1:1 → ProfessorProfile (if professor)
  1:M → StudentCourseEnrollment (enrollments)
  1:M → AttendanceRecord (attendance)
  1:M → ExamResult (grades)
  1:M → Submission (assignment submissions)
  1:M → StudentQuizResponse (quiz answers)
  1:M → CourseOffering (as lecturer)
  1:M → Announcement (as poster)
  1:M → StudentProgramEnrollment
  M:1 ← Department (professor's home dept)
  M:1 ← Program (student's home program)

CourseOffering ← Central to Academic Structure →
  
  M:1 ← Course (the course definition)
  M:1 ← Program (target program)
  M:1 ← User (lecturer)
  1:M → StudentCourseEnrollment (enrolled students)
  1:M → AttendanceRecord (attendance records)
  1:M → Assignment (assignments for this offering)
  1:M → Exam (exams for this offering)
  1:M → Quiz (quizzes for this offering)
  1:M → Schedule (class times)
  1:M → Announcement

StudentCourseEnrollment ← Junction Table →
  
  M:1 ← User (student)
  M:1 ← CourseOffering
```

---

## KEY DATA RELATIONSHIPS

### User-Course Relationships
```
User (Student) can:
- Be enrolled in 1+ StudentProgramEnrollment
- Be enrolled in 0+ StudentCourseEnrollment (per program)
- Have 0+ AttendanceRecord
- Have 0+ ExamResult (grades)
- Have 0+ Submission (assignment submissions)

User (Professor) can:
- Teach 1+ CourseOffering
- Create 1+ Assignment
- Create 1+ Exam
- Create 1+ Quiz
- Post 1+ Announcement

User (Admin) can:
- Manage any User
- Create any CourseOffering
- Post any Announcement
```

### Grade-Related Relationships
```
ExamResult ← Polymorphic-like relationship →
  
  One ExamResult links to:
  - assessment_id + assessment_type = "assignment"
  - assessment_id + assessment_type = "exam"
  - assessment_id + assessment_type = "quiz"
  
  Example:
  ExamResult {
    assessment_id: 10,
    assessment_type: "assignment",
    student_user_id: 1,
    score_obtained: 18,
    max_score: 20  // Lookup from Assessment
  }
```

### Attendance-Related Relationships
```
AttendanceRecord:
  - One record per student per course per date
  - status: present, absent, late, permission
  - Can be created by:
    - QR scan (status=present)
    - Manual entry (any status)
    - Auto-close (status=absent)
  
StudentCourseEnrollment:
  - attendance_score_manual field stores override
  - Relationship: 1 enrollment ← M AttendanceRecord
```

---

## SPECIAL FEATURES

### 1. Class Leader System
```
Feature: Designate student as class leader
Implementation:
  StudentCourseEnrollment.is_class_leader = true

Privileges:
  - Can view class attendance (read-only)
  - Can submit attendance on behalf of class
  - Sees "Class Leader" badge on profile
  - Gets leader-specific reports

Management:
  Professor toggles leader status:
  PATCH /professor/course-offering/{id}/student/{user_id}/toggle-leader
  
  Toggle function:
  if is_class_leader == true:
    is_class_leader = false
  else:
    is_class_leader = true
```

### 2. Soft Deletes
```
Tables with soft delete capability:
- faculties
- departments
- programs
- courses
- course_offerings
- rooms
- announcements

Implementation:
  Schema column: deleted_at (nullable timestamp)
  
  When deleting:
  UPDATE table SET deleted_at = now()
  // Record still exists but hidden from queries
  
  Query behavior:
  SELECT * from courses  // Excludes deleted
  SELECT * from courses.withTrashed()  // Includes deleted
  SELECT * from courses.onlyTrashed()  // Only deleted
```

### 3. Multi-Language Support
```
Bilingual fields (Khmer + English):

Courses:
  - title_km: "ច្ក្រម​ប្រូតូង​ក"
  - title_en: "Python Programming"

Assignments:
  - title_km, title_en
  - description (language-agnostic)

Exams, Quizzes:
  - title_km, title_en

Programs, Departments, Faculties:
  - name_km, name_en

Grading Categories:
  - name_km, name_en

System UI:
  - All user-facing text in Khmer & English
  - Laravel locale setting controls which displays
```

### 4. Export Functionality

#### A. DOCX Export (via PhpWord)
```
Supported Exports:
1. Gradebook: Full class gradebook with rankings
2. Student List: Roster with contact info
3. Attendance Report: Class attendance summary

Implementation:
  Use PhpOffice\PhpWord\PhpWord
  Create document structure
  Add tables, formatting
  Generate .docx file
  Download to browser

Example:
  GET /professor/course-offerings/{id}/export-gradebook
  Response: File download (.docx)
```

#### B. CSV Export
```
Supported Exports:
1. Grade data: For bulk import to external systems
2. Student list: For roster management
3. Attendance data: For analysis

Implementation:
  Use Maatwebsite\Excel
  Define columns
  Stream response as CSV
  Browser initiates download

Example:
  GET /assessment/{id}/export-csv
  Response: CSV file download
```

#### C. Import Functionality
```
CSV Import for Grades:
  POST /assessment/{id}/import-csv
  Body: multipart file upload
  
  System:
  1. Parse CSV file
  2. Validate format
  3. Match student IDs
  4. Validate scores
  5. Bulk update ExamResult records
  
  Use case: Grade entry from external spreadsheet
```

### 5. Real-time Features (Pusher)
```
Configured Broadcasting:
- config/broadcasting.php: Pusher driver
- routes/channels.php: Channel definitions

Channels:
  private-user.{user_id}
  - For user-specific notifications

Broadcasts:
  QrLoginSuccessful event
  - Broadcast when mobile authorizes login
  - Desktop listens to finalize

Real-time Updates:
  - Notification badges update instantly
  - Grades appear in real-time
  - Attendance counts update live
```

### 6. Telegram Bot Integration
```
Setup:
  1. Create Telegram bot via BotFather
  2. Store bot token in .env
  3. Register webhook at /professor/telegram/webhook

Features:
  - Send grade notifications
  - Bulk grade distribution
  - Two-way messaging possible

Example Flow:
  Professor posts grades
    → System checks each student's telegram_chat_id
    → Sends grade notification via bot
    → Student receives Telegram message
    → Student clicks link → Opens grade in system
```

### 7. AI Chat Assistant
```
Endpoint: POST /ai-chat/send

Features:
  - Rate-limited: 60 requests/minute
  - Uses OpenAI API
  - Stores conversation history
  - Available to all authenticated users

Storage:
  chat_messages table:
  - user_id: FK
  - ai_response: text
  - timestamp: created_at
  
Use Cases:
  - Academic assistance
  - General questions
  - Learning support
```

### 8. Google OAuth Integration
```
Endpoints:
  POST /auth/google/callback - Handle Google redirect
  POST /user/link-google - Link existing account

Flow:
  1. User clicks "Login with Google"
  2. Redirects to Google auth
  3. User approves
  4. Google redirects back with code
  5. System exchanges code for token
  6. Create/update user with google_id
  7. Login user

Linking:
  Logged-in user can link Google account
  Stores google_id in users.google_id
  Allows future Google logins
```

---

## ROUTE STRUCTURE

### Public Routes
```
GET / → Role-based redirect
GET /login → QR login form
GET /qr-refresh → Generate new QR
POST /qr-authorize → Mobile scans QR
GET /qr-login/finalize/{token} → Desktop finalization
POST /auth/google/callback → Google OAuth callback
GET /api/check-student/{code} → Check if student exists
```

### Admin Routes (`/admin/*`)
```
Dashboard:
  GET /admin/dashboard

User Management:
  GET /admin/users (view all)
  GET /admin/users/create
  POST /admin/users (store)
  GET /admin/users/{id}/edit
  PUT /admin/users/{id} (update)
  DELETE /admin/users/{id}
  GET /admin/users/show/{id}
  GET /admin/admin/users/export

Faculty Management:
  GET /admin/faculties
  GET /admin/faculties/create
  POST /admin/faculties
  GET /admin/faculties/{id}/edit
  PUT /admin/faculties/{id}
  DELETE /admin/faculties/{id}

Department Management:
  GET /admin/departments
  GET /admin/departments/create
  POST /admin/departments
  GET /admin/departments/{id}/edit
  PUT /admin/departments/{id}
  DELETE /admin/departments/{id}
  GET /admin/get-departments-by-faculty/{id}

Program Management:
  GET /admin/programs
  GET /admin/programs/create
  POST /admin/programs
  GET /admin/programs/{id}/edit
  PUT /admin/programs/{id}
  DELETE /admin/programs/{id}

Course Management:
  GET /admin/courses
  GET /admin/courses/create
  POST /admin/courses
  GET /admin/courses/{id}/edit
  PUT /admin/courses/{id}
  DELETE /admin/courses/{id}

Course Offering Management:
  GET /admin/course-offerings
  GET /admin/course-offerings/create
  POST /admin/course-offerings
  GET /admin/course-offerings/{id}/edit
  PUT /admin/course-offerings/{id}
  DELETE /admin/course-offerings/{id}
  GET /admin/enroll-student
  POST /admin/perform-enrollment
  GET /admin/show-course-offering/{id}

Room Management:
  GET /admin/rooms
  GET /admin/rooms/create
  POST /admin/rooms
  GET /admin/rooms/{id}
  GET /admin/rooms/{id}/edit
  PUT /admin/rooms/{id}
  DELETE /admin/rooms/{id}

Announcements:
  GET /admin/announcements
  GET /admin/announcements/create
  POST /admin/announcements/store
  GET /admin/announcements/{id}/edit
  PUT /admin/announcements/{id}/update
  DELETE /admin/announcements/{id}

Other:
  GET /admin/get-courses-by-program/{id}
  GET /admin/get-courses-by-program-and-generation
  GET /admin/users/search
```

### Professor Routes (`/professor/*`)
```
Dashboard:
  GET /professor/dashboard

Course Management:
  GET /professor/my-course-offerings
  GET /professor/all-course-offerings
  GET /professor/course-offerings/{id}/students
  GET /professor/course-offerings/{id}/students/{id}

Grade Management:
  GET /professor/course-offering/{id}/grades
  GET /professor/grades/all
  POST /professor/courses/{id}/grades/store
  POST /professor/assessments/{id}/grades
  GET /professor/course-offerings/{id}/export-gradebook
  POST /professor/grades/store/{id}
  GET /assessment/{id}/export-csv
  POST /assessment/{id}/import-csv
  GET /grades/edit/{student_id}/{course_id}
  POST /grades/attendance/update

Assessment Management:
  GET /professor/course-offering/{id}/assignments
  POST /professor/course-offering/{id}/assignments
  GET /professor/course-offerings/{id}/assignments/{id}/edit
  PUT /professor/course-offerings/{id}/assignments/{id}
  DELETE /professor/course-offerings/{id}/assignments/{id}
  
  GET /professor/course-offering/{id}/exams
  POST /professor/course-offering/{id}/exams
  GET /professor/course-offering/{id}/exams/{id}/edit
  PUT /professor/course-offering/{id}/exams/{id}
  DELETE /professor/course-offering/{id}/exams/{id}
  
  GET /professor/course-offerings/{id}/assessments/create
  POST /professor/course-offerings/{id}/assessments
  GET /assessments/{id}/edit/{type}
  PUT /assessments/{id}/{type}
  DELETE /assessments/{id}

Attendance Management:
  GET /professor/course-offerings/{id}/attendance
  POST /professor/course-offerings/{id}/attendance
  GET /professor/course-offerings/{id}/attendance-report
  POST /professor/verify-location
  POST /professor/attendance/precheck

Notifications:
  GET /professor/notifications
  GET /professor/notifications/create
  POST /professor/notifications/store
  GET /professor/notifications/{id}/edit
  PUT /professor/notifications/{id}
  DELETE /professor/notifications/{id}
  GET /professor/course-offerings/{id}/students

Communication:
  POST /professor/send-grade-telegram/{id}
  POST /professor/course-offering/{id}/send-all-telegram
  POST /professor/update-telegram
  POST /professor/announcements/{id}/mark-as-read

Profile:
  GET /professor/profile
  GET /professor/profile/edit
  PUT /professor/profile

Other:
  GET /professor/view-departments
  GET /professor/view-programs
  GET /professor/view-courses
  GET /professor/all-students
  GET /professor/my-schedule
  GET /professor/all-data
  GET /api/course-offerings-with-students
  PATCH /professor/course-offering/{id}/student/{user_id}/toggle-leader
  GET /professor/course-offering/{id}/export
  GET /professor/attendance/history
```

### Student Routes (`/student/*`)
```
Dashboard:
  GET /student/dashboard

Grades & Courses:
  GET /student/my-grades
  GET /student/my-enrolled-courses
  GET /student/my-schedule
  GET /student/{id}/enrolled-courses
  GET /student/available-programs
  GET /student/available-courses
  POST /student/enroll-self

Assignments:
  GET /student/my-assignments

Exams:
  GET /student/my-exams

Quizzes:
  GET /student/my-quizzes
  GET /student/quizzes/{id}
  POST /student/quizzes/{id}/submit

Attendance:
  GET /student/my-attendance
  POST /student/process-scan (QR scan)
  GET /student/class-leader/course/{id}/attendance (if leader)
  POST /student/class-leader/course/{id}/attendance
  GET /student/leader/attendance-report/{id}

Notifications & Announcements:
  GET /student/notifications
  PATCH /student/notifications/{id}/read
  PATCH /student/notifications/read-all
  PATCH /student/announcements/{id}/read

Profile:
  GET /student/profile
  GET /student/profile/edit
  PUT /student/profile

Rooms:
  GET /student/rooms

Other:
  GET /student/scan
  POST /student/update-telegram
```

### Shared Routes (All Roles)
```
Profile:
  GET /profile
  PATCH /profile
  DELETE /profile
  POST /profile/update-picture

AI Chat:
  GET /ai-chat
  POST /ai-chat/send
  GET /ai/history
  POST /ai/clear-history
  POST /ai/send

Authentication:
  All standard Laravel Breeze routes
  logout, verify email, etc.
```

---

## SUMMARY: KEY TAKEAWAYS

### Data Flow Overview
```
1. Student registers/Enrolls
   ↓
2. Student gets assigned to Course Offering
   ↓
3. Professor views enrolled students in gradebook
   ↓
4. Professor marks attendance (QR or manual)
   ↓
5. Professor creates assessments (assignments, exams, quizzes)
   ↓
6. Students complete assessments
   ↓
7. Professor enters grades
   ↓
8. System calculates final grade
   ↓
9. System notifies students (Database/Telegram)
   ↓
10. Student views grades and ranking
```

### Role-Based Access Control
```
Admin: Full system management, CRUD everything
Professor: Course management, grading, attendance, student communication
Student: Learning, assignment submission, grade/attendance viewing
```

### Assessment Scoring
```
Total = Attendance (15) + Assignments (20) + Exams (50) + Quizzes (10)
Grade = A(85+), B+(80-84), B(70-79), C+(65-69), C(50-64), F(<50)
Failure if: Midterm<9 OR Final<24 OR Assignment<9 OR Attendance<9
```

### Real-time Systems
- **Pusher**: Instant notification delivery
- **QR Attendance**: Immediate attendance recording
- **QR Login**: Cross-device authorization
- **Telegram**: Grade push notifications

### Export/Import
- **DOCX**: Professional gradebook/roster documents
- **CSV**: Data interchange with external systems
- **Bulk Import**: Grades from spreadsheets

---

**End of Document**
