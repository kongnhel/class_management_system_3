# Prompt: Generate Professor Role Flowchart
## National Meanchey University — Class Management System

Copy the prompt below and paste into ChatGPT / Claude / Gemini.

---

## BEGIN PROMPT ————————————————————————————

Create a **Professor Role Process Flowchart** for a **Class Management System** at National Meanchey University. Use **Mermaid flowchart TD** syntax.

Use Khmer text for the title and English for the flowchart nodes. Style it like an academic thesis diagram with light blue filled nodes.

---

### TITLE (Khmer):
```
៨.២.២ រាល់ដំណើរការនៃមុខនាទីគ្រូបង្រៀន៖ Flowchart
ច. វិធីសាស្រ្តសម្រាប់ការគ្រប់គ្រងការបង្រៀនដោយប្រើ Role Professor
```

### SUBTITLE:
```
ប្រភេទ ទី ២.២ ៖ Flowchart Professor Role
```

---

### FLOWCHART LOGIC (based on this project's actual code):

```
Start
  ↓
Professor Login (Email + Password)
  ↓
Professor Dashboard
  ├── Course Offerings (My Courses)
  │     ├── View List
  │     ├── View Details
  │     └── View Schedule
  ├── Attendance Management
  │     ├── Start QR Session → Display QR Code
  │     │     ↓
  │     │   Student Scan QR → Location Check → GPS OK?
  │     │     ├── No → Reject (Show error)
  │     │     └── Yes → Validate Token → Token Valid?
  │     │           ├── No → Reject (Show error)
  │     │           └── Yes → Record Attendance (Present)
  │     │
  │     ├── Manual Attendance
  │     │     ├── Select Student
  │     │     ├── Select Status (Present/Late/Permission/Absent)
  │     │     └── Submit
  │     │
  │     ├── Close Session
  │     │     └── Auto-mark absent for students without records
  │     │
  │     ├── View History
  │     │     └── Filter by Date Range
  │     │
  │     └── Export Attendance (Excel)
  │
  ├── Grade Management
  │     ├── View Grades (Table with profile pics)
  │     ├── Enter/Edit Grades
  │     │     ├── Select Assessment Type
  │     │     ├── Enter Score
  │     │     └── Auto-save (AJAX)
  │     │
  │     ├── Calculate Final Grades
  │     │     ├── Attendance (15%)
  │     │     │     └── Auto-calc: 2 absences = -1 point
  │     │     ├── Assignments (20%)
  │     │     ├── Quizzes (15%)
  │     │     ├── Midterm (25%)
  │     │     ├── Final (25%)
  │     │     └── Letter Grade (A, B+, B, C+, C, D+, D, F)
  │     │
  │     ├── Import Grades (Excel)
  │     │     └── Validate Format
  │     │
  │     └── Export Grades (Excel)
  │           └── Download with max scores in header
  │
  ├── Assignments
  │     ├── View List
  │     ├── Create Assignment
  │     │     ├── Title
  │     │     ├── Description
  │     │     ├── Due Date
  │     │     └── Max Score
  │     ├── Edit/Delete
  │     └── View Submissions
  │
  ├── Exams
  │     ├── View List
  │     ├── Create Exam
  │     │     ├── Title
  │     │     ├── Type (Midterm/Final)
  │     │     ├── Date
  │     │     └── Max Score
  │     ├── Edit/Delete
  │     └── Enter Scores
  │
  ├── Quizzes
  │     ├── View List
  │     ├── Create Quiz
  │     │     ├── Title
  │     │     ├── Questions
  │     │     └── Options
  │     ├── Edit/Delete
  │     └── View Responses
  │
  ├── My Schedule
  │     └── View Weekly Schedule
  │
  ├── Notifications
  │     ├── View All
  │     ├── Send Telegram Notification
  │     │     └── Select Course
  │     │     └── Compose Message
  │     └── Mark as Read
  │
  └── Profile Management
        ├── View Profile
        ├── Edit Profile
        └── Upload Picture
```

---

### DETAILED FLOWCHART NODES:

1. **Start** (Oval)
2. **Professor Login** (Rectangle) — Email + Password authentication
3. **Professor Dashboard** (Rectangle) — Main hub showing:
   - Total courses assigned
   - Today's schedule
   - Recent notifications
   - Quick action buttons

4. **Course Offerings** (Rectangle) — My courses list
   - **View Details** (Rectangle) — Course info, enrolled students
   - **View Schedule** (Rectangle) — Weekly class schedule

5. **Attendance Management** (Rectangle) — Attendance module
   - **Start QR Session** (Rectangle) — Generate QR code with 15-second expiry
     - **Student Scan QR** (Rectangle) — Student scans via mobile
     - **Location Check** (Diamond) — GPS verification
       - No → **Reject** (Rectangle) — "មិននៅក្នុងមហាវិទ្យាល័យ"
       - Yes → **Validate Token** (Diamond) — Token valid?
         - No → **Reject** (Rectangle) — "Token ផុតកំណត់"
         - Yes → **Record Attendance** (Rectangle) — Mark as Present

   - **Manual Attendance** (Rectangle) — Add record manually
     - **Select Student** (Rectangle) — Dropdown with profile pics
     - **Select Status** (Rectangle) — Present/Late/Permission/Absent
     - **Submit** (Rectangle) — Save to database

   - **Close Session** (Rectangle) — End attendance session
     - **Auto-mark Absent** (Rectangle) — System marks missing students

   - **View History** (Rectangle) — Attendance records
     - **Filter by Date** (Rectangle) — Date range picker

   - **Export Attendance** (Rectangle) — Download Excel

6. **Grade Management** (Rectangle) — Grades module
   - **View Grades** (Rectangle) — Table with profile pics
   - **Enter/Edit Grades** (Rectangle) — Grade entry form
     - **Select Assessment** (Rectangle) — Type dropdown
     - **Enter Score** (Rectangle) — Number input
     - **Auto-save** (Rectangle) — AJAX save

   - **Calculate Final Grades** (Rectangle) — Grade calculation
     - **Attendance** (Rectangle) — 15% weight
     - **Assignments** (Rectangle) — 20% weight
     - **Quizzes** (Rectangle) — 15% weight
     - **Midterm** (Rectangle) — 25% weight
     - **Final** (Rectangle) — 25% weight
     - **Letter Grade** (Rectangle) — A, B+, B, C+, C, D+, D, F

   - **Import Grades** (Rectangle) — Upload Excel
   - **Export Grades** (Rectangle) — Download Excel

7. **Assignments** (Rectangle) — Assignments module
   - **View List** (Rectangle) — All assignments
   - **Create Assignment** (Rectangle) — Add new
   - **Edit/Delete** (Rectangle) — Modify
   - **View Submissions** (Rectangle) — Student work

8. **Exams** (Rectangle) — Exams module
   - **View List** (Rectangle) — All exams
   - **Create Exam** (Rectangle) — Add new
   - **Edit/Delete** (Rectangle) — Modify
   - **Enter Scores** (Rectangle) — Grade exams

9. **Quizzes** (Rectangle) — Quizzes module
   - **View List** (Rectangle) — All quizzes
   - **Create Quiz** (Rectangle) — Add new
   - **Edit/Delete** (Rectangle) — Modify
   - **View Responses** (Rectangle) — Student answers

10. **My Schedule** (Rectangle) — Weekly schedule view

11. **Notifications** (Rectangle) — Notifications module
    - **View All** (Rectangle) — Notification list
    - **Send Telegram** (Rectangle) — Send message
    - **Mark as Read** (Rectangle) — Mark notification

12. **Profile Management** (Rectangle) — Profile module
    - **View Profile** (Rectangle) — Profile info
    - **Edit Profile** (Rectangle) — Update info
    - **Upload Picture** (Rectangle) — Profile picture

---

### VISUAL STYLE:

- Use `flowchart TD` (top-down)
- Start/Stop nodes: **Oval** shape, filled with light blue (`#B0C4DE`)
- Process nodes (Dashboards, Forms): **Rectangle**, filled light blue
- Decision nodes (Location Check, Token Valid): **Diamond**, filled light blue
- All nodes have black text
- Arrows are black with labels (Yes/No, action names)
- Title and subtitle in Khmer at the top
- Bottom right: "អ្នកនិពន្ធ" (Author)
- Clean, academic thesis style

## END PROMPT ————————————————————————————

---

## How to Use

1. Copy everything between `BEGIN PROMPT` and `END PROMPT`
2. Paste into ChatGPT, Claude, or Gemini
3. Copy the Mermaid code to [mermaid.live](https://mermaid.live) to render
4. Export as PNG/SVG for your thesis

## What the Diagram Will Look Like (Simplified)

```
                              ┌─────────┐
                              │  Start  │
                              └────┬────┘
                                   │
                              ┌────┴────┐
                              │Professor│
                              │  Login  │
                              └────┬────┘
                                   │
                              ┌────┴────┐
                              │Dashboard│
                              └────┬────┘
                                   │
         ┌─────────────────────────┼─────────────────────────┐
         │                         │                         │
    ┌────┴────┐              ┌─────┴─────┐              ┌────┴────┐
    │ Course  │              │Attendance │              │  Grade  │
    │Offerings│              │Management │              │Management│
    └────┬────┘              └─────┬─────┘              └────┬────┘
         │                         │                         │
    ┌────┴────┐              ┌─────┴─────┐              ┌────┴────┐
    │  View   │              │ Start QR  │              │  View   │
    │ Details │              │  Session  │              │ Grades  │
    └────┬────┘              └─────┬─────┘              └────┬────┘
         │                         │                         │
    ┌────┴────┐              ┌─────┴─────┐              ┌────┴────┐
    │  View   │              │ Student   │              │ Enter/  │
    │Schedule │              │ Scan QR   │              │  Edit   │
    └─────────┘              └─────┬─────┘              └────┬────┘
                                   │                         │
                            ┌──────┴──────┐            ┌─────┴─────┐
                            │   Location  │            │ Calculate │
                            │    Check    │            │   Final   │
                            └──┬───────┬──┘            └─────┬─────┘
                         No    │       │  Yes                 │
                    ┌──────────┘       └──────────┐          │
                    │                             │    ┌─────┴─────┐
              ┌─────┴─────┐                 ┌─────┴──┐ │  Import/  │
              │  Reject   │                 │Validate│ │  Export   │
              └───────────┘                 │ Token  │ └───────────┘
                                            └──┬─────┘
                                         No    │    │ Yes
                                    ┌──────────┘    └──────────┐
                                    │                          │
                              ┌─────┴─────┐              ┌─────┴─────┐
                              │  Reject   │              │  Record   │
                              └───────────┘              │ Attendance│
                                                         └───────────┘
```

## Code References

- **Routes**: `routes/web.php` (lines 380-500 for professor routes)
- **Controllers**: `app/Http/Controllers/professor/`
- **Views**: `resources/views/professor/`
- **Attendance API**: `app/Http/Controllers/professor/AttendanceApiController.php`
- **Grade Export**: `app/Exports/ProfessorGradeExcelExport.php`
- **Grade Import**: `app/Imports/ProfessorGradeImport.php`
- **Telegram**: `app/Services/TelegramService.php`
