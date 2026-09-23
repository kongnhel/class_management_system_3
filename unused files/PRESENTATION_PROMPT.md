# Class Management System - Presentation Prompt

## រូបភាព៖ ស្លាកសាលា NMU

```
Create a professional academic presentation (15-20 slides) for my graduation thesis project at National Meanchey University. Use clean, modern design with university colors (green/white theme). Each slide should have Khmer titles and English content.

---

SLIDE 1 — Title Slide
- រូបភាព៖ ស្លាកសាលា NMU
- Title: ប្រព័ន្ធគ្រប់គ្រងថ្នាក់សិក្សា (Class Management System)
- Subtitle: កម្មវិធីសម្រាប់ក្រសួងអប់រំ — ឆ្នាំសិក្សា ២០២៥-២០២៦
- អ្នកនិពន្ធ៖ ឈ្មោះរបស់អ្នក
- ប្រធានាធិបតី៖ ឈ្មោះអ្នកជំនាញការ

---

SLIDE 2 — បញ្ហាស្រាវជ្រាវ (Problem Statement)
- សាលារៀនមានបញ្ហាក្នុងការគ្រប់គ្រងវត្តមានសិស្សដោយដៃ
- ការគណនាពិន្ទុដោយដៃងាយមានកំហុស
- មិនមានប្រព័ន្ធផ្ទៀងផ្ទាត់ភាពត្រឹមត្រូវនៃវត្តមាន
- សិស្សពិបាកក្នុងការតាមដានពិន្ទុ និងវត្តមានរបស់ខ្លួន

---

SLIDE 3 — គោលបែងការស្រាវជ្រាវ (Research Objectives)
- បង្កើតប្រព័ន្ធគ្រប់គ្រងថ្នាក់សិក្សាដែលអាចប្រើប្រាស់បានតាមអ៊ីនធឺណិត
- ប្រើប្រាស់បច្ចេកវិទ្យា QR Code សម្រាប់ការស្រង់វត្តមាន
- ប្រព័ន្ធគណនាពិន្ទុស្វ័យប្រវត្តិ
- ការជូនដំណឹងតាម Telegram

---

SLIDE 4 — បច្ចេកវិទ្យាដែលប្រើប្រាស់ (Technologies Used)
- Backend: Laravel 12 (PHP 8.2)
- Frontend: Tailwind CSS, Alpine.js, Vite
- Database: MySQL (Aiven Cloud)
- Authentication: Laravel Breeze + Firebase Google OAuth
- Real-time: Laravel Echo + Pusher
- PDF: DomPDF
- Excel: Maatwebsite/Excel
- Deployment: Vercel + Docker
- Chat: AI Chatbot Integration

---

SLIDE 5 — រចនាសម្ព័ន្ធប្រព័ន្ធ (System Architecture)
- 3D Diagram showing:
  ├── Admin Panel
  ├── Professor Panel
  ├── Student Panel
  ├── Database (MySQL)
  ├── External Services (Firebase, Telegram, Cloudinary)
  └── Real-time (Pusher)

---

SLIDE 6 — អ្នកប្រើប្រាស់ និងតួនាទី (User Roles)
- Admin: គ្រប់គ្រងសិស្ស គ្រូ មុខវិជ្ជា ការផ្តល់ជូន បន្ទប់ ការប្រកាស
- Professor: គ្រប់គ្រងវត្តមាន ពិន្ទុ កិច្ចការ ប្រឡង ឃ្វីស កាលវិភាគ
- Student: មើលមុខវិជ្ជា ពិន្ទុ វត្តមាន កាលវិភាគ ការជូនដំណឹង

---

SLIDE 7 — Database Schema (ER Diagram)
- Users, StudentProfiles, ProfessorProfiles
- Programs, Courses, CourseOfferings
- Schedules, Rooms
- AttendanceRecords, AttendanceProfessors
- Assignments, Exams, Quizzes
- ExamResults, GradingCategories
- Notifications, Announcements

---

SLIDE 8 — Authentication System
- Email + Password login
- Google OAuth (Firebase)
- QR Code Login (Mobile scan → Desktop authorize)
- Role-based middleware (admin/professor/student)

---

SLIDE 9 — QR Code Attendance System (Feature 1)
- Professor starts attendance session → QR code generated
- QR refreshes every 15 seconds
- Student scans QR with mobile camera
- GPS location verification (within university campus)
- System records attendance automatically
- Absent students auto-marked when session closes

---

SLIDE 10 — Attendance Scoring Formula
- Attendance = 15% of total grade
- Formula: 2 អវត្តមាន = -1 ពិន្ទុ, 4 សិទ្ធិ = -1 ពិន្ទុ
- Real-time dashboard with student photos
- Manual attendance option for professors
- Class leader attendance feature

---

SLIDE 11 — Grading System
- Multiple assessment types: Assignments (20%), Quizzes (15%), Midterm (25%), Final (25%), Attendance (15%)
- Letter grades: A, B+, B, C+, C, D+, D, F
- Failing logic: Final < 24 OR Midterm < 9 OR Assignment < 9 OR Attendance < 9 → F
- Excel export with university template (max scores in header)
- Excel import from university format

---

SLIDE 12 — Student Progression
- Automatic semester progression
- Students with no scores/courses automatically marked as F
- Promotion criteria based on GPA
- Bulk student progression processing

---

SLIDE 13 — Notification System
- Database-backed notifications
- Telegram bot integration for grade notifications
- Daily schedule notification via Telegram
- Professor notification for attendance/grades

---

SLIDE 14 — AI Chatbot Assistant
- Conversational AI for student queries
- Gender-aware pronouns (Khmer)
- Course-aware context (knows enrolled courses)
- Grade lookup and schedule queries

---

SLIDE 15 — Export & Import
- Grade Export: Excel with university template (header, max scores, signatures)
- Student List Export: Excel format
- Bulk Import: Users via Excel, Students via Excel
- Attendance Export: Excel format

---

SLIDE 16 — UI/UX Design
- Responsive design (mobile, tablet, desktop)
- Dark sidebar navigation
- Profile pictures from multiple sources
- Card-based dashboard layout
- Khmer language support
- Print-friendly layouts

---

SLIDE 17 — Security & Performance
- Soft delete for data recovery
- Unique constraint on enrollments
- Conflict detection for room/lecturer scheduling
- Timezone handling (Asia/Phnom_Penh)
- SQL injection protection via Eloquent ORM
- CSRF protection via Laravel tokens

---

SLIDE 18 — Testing & Quality
- Pest PHP testing framework
- Feature and Unit tests
- Laravel Pint code formatting
- Static analysis

---

SLIDE 19 — Deployment
- Vercel deployment (serverless)
- Docker containerization
- MySQL cloud database (Aiven)
- Environment-based configuration

---

SLIDE 20 — លទ្ធផល (Results & Conclusion)
- កាត់បន្ថយពេលវេលាគ្រប់គ្រងវត្តមាន
- កាត់បន្ថយកំហុសក្នុងការគណនាពិន្ទុ
- សិស្សអាចតាមដានពិន្ទុ និងវត្តមានរបស់ខ្លួន
- កាត់បន្ថយការប្រើក្រដាស
- ងាយស្រួលក្នុងការប្រើប្រាស់

---

SLIDE 21 — អនាគត (Future Work)
- Mobile app (React Native / Flutter)
- Online payment integration
- Multi-language support
- Advanced analytics & reports
- Integration with MoEYS systems

---

SLIDE 22 — សេចក្តីថ្លែងអំណរគុណ (Thank You)
- សូមអរគុណសម្រាប់ការស្តាប់
- សំណួរ និងចម្លើយ

---

DESIGN NOTES:
- Use green (#059669) as primary color
- White background with subtle shadows
- University logo in top-left corner of each slide
- Khmer font: "Khmer OS Battambang" for titles
- English font: "Inter" or "Roboto" for body text
- Consistent spacing and alignment
- Include diagrams/illustrations where possible
- Each slide: max 5-6 bullet points
- Professional academic tone
```

## How to Use

1. Copy the prompt above
2. Paste into ChatGPT, Claude, or Gemini
3. Ask for PowerPoint (.pptx) format
4. Edit the Khmer text to match your name/advisor

## Tips

- Ask for Mermaid diagrams for architecture and ER diagrams
- Request Khmer font support
- Ask for consistent green/white theme
