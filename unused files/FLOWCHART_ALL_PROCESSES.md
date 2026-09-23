# Prompt: Generate All System Flowcharts
## National Meanchey University — Class Management System
## For Thesis/Sarana Book

Copy the prompt below and paste into ChatGPT / Claude / Gemini.

---

## BEGIN PROMPT ————————————————————————————

Create **15 Flowcharts** for a **Class Management System** thesis at National Meanchey University. Use **Mermaid flowchart TD** syntax. Each flowchart should be numbered and titled in Khmer with English node labels.

Use academic thesis style: light blue filled nodes, black text, black arrows with labels.

---

## FLOWCHART 1: ការចូលប្រើប្រាស់ប្រព័ន្ធ (Login Process)

```
Start → Has logged in? → Yes → Dashboard (Admin/Professor/Student) → Stop
                ↓ No
        Login Form (Email/Student ID + Password)
                ↓
        Authenticate Credentials
          ↓ No        ↓ Yes
    Back to Login   Authorize Role
                     ↓       ↓       ↓
                  Admin   Professor Student
                     ↓       ↓       ↓
                  Dashboard Dashboard Dashboard → Stop
```

---

## FLOWCHART 2: ការគ្រប់គ្រងមហាវិទ្យាល័យ (Faculty Management)

```
Start → Admin Dashboard → Manage Faculties
  ├── Add Faculty → Fill Form (Name KM/EN, Dean) → Validate → Save to DB → Success Message → Stop
  ├── Edit Faculty → Select Faculty → Update Form → Validate → Update DB → Success Message → Stop
  └── Delete Faculty → Confirm Dialog → Delete from DB (cascade: departments, programs, courses) → Success Message → Stop
```

---

## FLOWCHART 3: ការគ្រប់គ្រងដេប៉ាតឺម៉ង់ (Department Management)

```
Start → Admin Dashboard → Manage Departments
  ├── Add Department → Select Faculty → Fill Form (Name KM/EN, Head) → Validate → Save to DB → Success Message → Stop
  ├── Edit Department → Select Department → Update Form → Validate → Update DB → Success Message → Stop
  └── Delete Department → Confirm Dialog → Delete from DB (cascade: programs, courses) → Success Message → Stop
```

---

## FLOWCHART 4: ការគ្រប់គ្រងកម្មវិធីសិក្សា (Program Management)

```
Start → Admin Dashboard → Manage Programs
  ├── Add Program → Select Department → Fill Form (Name KM/EN, Degree Level, Duration) → Validate → Save to DB → Success Message → Stop
  ├── Edit Program → Select Program → Update Form → Validate → Update DB → Success Message → Stop
  └── Delete Program → Confirm Dialog → Delete from DB (cascade: courses) → Success Message → Stop
```

---

## FLOWCHART 5: ការគ្រប់គ្រងមុខវិជ្ជា (Course Management)

```
Start → Admin Dashboard → Manage Courses
  ├── Add Course → Select Department → Fill Form (Title KM/EN, Credits) → Validate → Save to DB + Auto-create 4 Grading Categories → Success Message → Stop
  ├── Edit Course → Select Course → Update Form → Validate → Update DB → Success Message → Stop
  └── Delete Course → Confirm Dialog → Delete from DB → Success Message → Stop
```

---

## FLOWCHART 6: ការគ្រប់គ្រងការបង្រៀន (Course Offering Management)

```
Start → Admin Dashboard → Manage Course Offerings
  ├── Add Offering → Select Course + Program + Generation + Semester + Room + Lecturer → Fill Form → Validate → Save to DB → Auto-enroll matching students → Create schedules → Success Message → Stop
  ├── Edit Offering → Select Offering → Update Form → Validate → Update DB → Sync programs & schedules → Success Message → Stop
  └── Delete Offering → Confirm Dialog → Delete from DB (cascade: enrollments, assignments, exams, quizzes) → Success Message → Stop
```

---

## FLOWCHART 7: ការគ្រប់គ្រងបន្ទប់ (Room Management)

```
Start → Admin Dashboard → Manage Rooms
  ├── Add Room → Fill Form (Room Number, Capacity, Location, Type, WiFi QR) → Upload WiFi QR to ImageKit → Validate → Save to DB → Success Message → Stop
  ├── Edit Room → Select Room → Update Form → Validate → Update DB → Success Message → Stop
  └── Delete Room → Confirm Dialog → Delete from DB → Success Message → Stop
```

---

## FLOWCHART 8: ការគ្រប់គ្រងអ្នកប្រើប្រាស់ (User Management)

```
Start → Admin Dashboard → Manage Users
  ├── Add User → Select Role (Admin/Professor/Student)
  │     ├── Admin/Professor → Fill Form (Name, Email, Password) → Save to DB → Create UserProfile → Success Message → Stop
  │     └── Student → Fill Form (Name, Email, Password, Program, Generation) → Save to DB → Create StudentProfile + StudentProgramEnrollment → Auto-generate Student ID Code → Success Message → Stop
  ├── Edit User → Select User → Update Form → Validate → Update DB → Update Profile → Success Message → Stop
  ├── Delete User → Confirm Dialog → Delete from DB (Soft Delete) → Success Message → Stop
  └── Export Users → Select Role Filter → Generate Excel → Download → Stop
```

---

## FLOWCHART 9: ការចុះឈ្មោះសិស្ស (Student Enrollment)

```
Start → Admin Dashboard → Enroll Student
  ├── Admin Enroll → Select Student + Course Offering → Validate (not already enrolled) → Save to student_course_enrollments → Success Message → Stop
  └── Student Self-Enroll → View Available Courses → Select Course Offering → Validate (is_open_for_self_enrollment) → Save to enrollment → Success Message → Stop
```

---

## FLOWCHART 10: ការគ្រប់គ្រងវត្តមាន (Attendance Management)

```
Start → Professor Dashboard → Select Course Offering → Manage Attendance
  ├── QR Code Scan → Student scans QR → Validate token + enrollment → Save attendance (present) → Update attendance score → Stop
  ├── Manual Attendance → Select students → Set status (present/absent/late/permission) → Save to DB → Update attendance score → Stop
  ├── Class Leader Attendance → Leader selects students → Set status → Save to DB → Update attendance score → Stop
  └── Close Attendance → Auto-mark non-scanners as absent → Calculate attendance score → Stop

Attendance Score Formula: 15 - floor(absent_count/2) - floor(permission_count/4)
```

---

## FLOWCHART 11: ការគ្រប់គ្រងកិច្ចការ (Assignment Management)

```
Start → Professor Dashboard → Select Course Offering → Manage Assignments
  ├── Create Assignment → Fill Form (Title, Description, Due Date, Max Score, Grading Category) → Validate → Save to DB → Success Message → Stop
  ├── Edit Assignment → Select Assignment → Update Form → Validate → Update DB → Success Message → Stop
  ├── Delete Assignment → Confirm Dialog → Delete from DB (cascade: submissions, exam_results) → Success Message → Stop
  └── Grade Submissions → View Submissions → Select Submission → Enter Grade + Feedback → Save to DB → Stop
```

---

## FLOWCHART 12: ការគ្រប់គ្រងប្រឡង (Exam Management)

```
Start → Professor Dashboard → Select Course Offering → Manage Exams
  ├── Create Exam → Fill Form (Title, Date, Duration, Max Score, Grading Category) → Validate → Save to DB → Success Message → Stop
  ├── Edit Exam → Select Exam → Update Form → Validate → Update DB → Success Message → Stop
  ├── Delete Exam → Confirm Dialog → Delete from DB (cascade: exam_results) → Success Message → Stop
  └── Enter Grades → Select Exam → Enter scores per student → Save to exam_results → Success Message → Stop
```

---

## FLOWCHART 13: ការគ្រប់គ្រងពិន្ទុ (Grading Process)

```
Start → Professor Dashboard → Select Course Offering → Manage Grades
  ├── Enter Grades → Select Assessment (Assignment/Exam/Quiz) → Enter scores per student → Save to exam_results → Stop
  ├── Import Grades → Upload CSV File → Parse & validate → Bulk save to exam_results → Success Message → Stop
  ├── Export Grades → Select Course Offering → Generate CSV/DOCX → Download → Stop
  └── Compute Final Grades → Read all exam_results + attendance scores → Calculate total = attendance_score + sum(assessment_scores) → Convert to letter grade → Save to student_course_enrollments.final_grade → Stop

Grade Formula: Total = Attendance Score (15%) + Midterm (15%) + Assignment (20%) + Final (50%)
Passing: Final >= 24, Total >= 50
```

---

## FLOWCHART 14: ការជូនដំណឹង (Notification Process)

```
Start → Professor Dashboard → Manage Notifications
  ├── Create Notification → Fill Form (Title, Message, Select Recipients) → Save to notifications table → Success Message → Stop
  ├── Send via Telegram → Select students → Read telegram_chat_id → Send via Telegram Bot API → Success Message → Stop
  └── View Notifications → Read from notifications + announcements → Display combined feed → Mark as read → Stop
```

---

## FLOWCHART 15: ការជំនួយ AI (AI Chat Assistant)

```
Start → Any User → Open AI Chat
  ├── Send Message → Save user message to chat_messages → Gather context (user info, courses, grades, attendance) → Send to Dify AI API → Save AI response → Display response → Stop
  ├── View History → Read last 50 messages from chat_messages → Display → Stop
  └── Clear History → Delete all messages from chat_messages → Stop
```

---

## VISUAL STYLE FOR ALL FLOWCHARTS:

- Use `flowchart TD` (top-down direction)
- Start/Stop nodes: **Oval** shape, filled light blue (`#B0C4DE`)
- Process nodes: **Rectangle**, filled light blue
- Decision nodes: **Diamond**, filled light blue
- All nodes have black text
- Arrows are black with labels (Yes/No, role names, actions)
- Numbered titles: "១. Flowchart ការចូលប្រើប្រាស់ប្រព័ន្ធ"
- Bottom right: "អ្នកនិពន្ធ" (Author)
- Clean, academic thesis style

## END PROMPT ————————————————————————————

---

## How to Use

1. Copy everything between `BEGIN PROMPT` and `END PROMPT`
2. Paste into ChatGPT/Claude (it may need multiple rounds for all 15)
3. If output is cut off, say: **"Continue from where you stopped"**
4. Copy each Mermaid code to [mermaid.live](https://mermaid.live) to render
5. Export as PNG/SVG for your thesis

## Tips

- If you want **more detail** on a specific flowchart, say: **"Expand Flowchart 10 (Attendance) with more detail"**
- If you want **Data Flow Diagram (DFD)** instead, say: **"Convert to DFD Level 1"**
- If you want **PlantUML syntax**, say: **"Convert to PlantUML"**
