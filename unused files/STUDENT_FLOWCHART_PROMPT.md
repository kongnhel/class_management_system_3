# Student Role - Flowchart Prompt

## រូបភាព៖ Process Flow សិស្ស

```
Create a comprehensive flowchart document for the STUDENT ROLE of a Class Management System at National Meanchey University. Use Mermaid flowchart syntax. Each flowchart should be numbered, have a Khmer title, and show decision points (diamonds), processes (rectangles), and start/end points (rounded). Use academic thesis style.

---

## FLOWCHART 1: Student Login Process

```mermaid
flowchart TD
    A([ចូលប្រើប្រាស់ / Start]) --> B{ជ្រើសរើសវិធីចូល?}
    B -->|Email + Password| C[បញ្ចូល Email និងពាក្យសម្ងាត់]
    B -->|Google OAuth| D[ចុច "ចូលជាមួយ Google"]
    B -->|QR Code| E[បង្ហាញ QR Code នៅ Desktop]
    
    C --> F{ពិនិត្យពាក្យសម្ងាត់?}
    F -->|ត្រឹមត្រូវ| G[ពិនិត្យតួនាទីអ្នកប្រើប្រាស់]
    F -->|មិនត្រឹមត្រូវ| H[បង្ហាញសារកំហុស]
    H --> C
    
    D --> I{Google Auth ជោគជ័យ?}
    I -->|បាទ/ចាស| G
    I -->|ទេ| J[បង្ហាញសារកំហុស]
    
    E --> K[សិស្សស្កែន QR ដោយទូរស័ព្ទ]
    K --> L{Token ត្រឹមត្រូវ?}
    L -->|បាទ/ចាស| M[Cache authorized_user]
    L -->|ទេ| N[បង្ហាញសារកំហុស]
    M --> O[Broadcast QrLoginSuccessful Event]
    O --> P[Desktop: finalizeLogin]
    P --> G
    
    G --> Q{តួនាធី?}
    Q -->|admin| R[រដ្ឋបាលសាលា Dashboard]
    Q -->|professor| S[គ្រូ Dashboard]
    Q -->|student| T[សិស្ស Dashboard]
    
    T --> U([ចប់ / End])
    R --> U
    S --> U
```

---

## FLOWCHART 2: Student Dashboard

```mermaid
flowchart TD
    A([សិស្សចូល / Start]) --> B[ទាញយកទិន្នន័យសិស្ស]
    B --> C[គណនាវត្តមាន: Present, Absent, Permission, Late]
    C --> D[ទាញយកមុខវិជ្ជាថ្ងៃនេះ]
    D --> E[ទាញយកកាលវិភាគថ្ងៃនេះ]
    E --> F[ទាញយកកម្មវត្ថុសិស្ស]
    F --> G[គណនា GPA និងពិន្ទុមធ្យម]
    G --> H[គណនាការឈ្នះរង្វាន់]
    H --> I[ទាញយកការជូនដំណឹង]
    I --> J[បង្ហាញ Dashboard]
    J --> K{សិស្សជ្រើសរើស?}
    
    K -->|មុខវិជ្ជារបស់ខ្ញុំ| L[ទៅកាន់ My Enrolled Courses]
    K -->|ពិន្ទុ| M[ទៅកាន់ My Grades]
    K -->|វត្តមាន| N[ទៅកាន់ My Attendance]
    K -->|កាលវិភាគ| O[ទៅកាន់ My Schedule]
    K -->|ការជូនដំណឹង| P[ទៅកាន់ Notifications]
    K -->|កែប្រែព័ត៌មាន| Q[ទៅកាន់ Profile]
    K -->|AI Chatbot| R[បើក AI Chatbot]
    K -->|Scan QR| S[ទៅកាន់ QR Scanner]
    
    L --> T([ចប់])
    M --> T
    N --> T
    O --> T
    P --> T
    Q --> T
    R --> T
    S --> T
```

---

## FLOWCHART 3: Course Enrollment Process

```mermaid
flowchart TD
    A([ចុះឈ្មោះមុខវិជ្ជា / Start]) --> B{ជ្រើសរើសវិធី?}
    
    B -->|ចុះឈ្មោះខ្លួនឯង| C[មើលមុខវិជ្ជាដែលមាន]
    C --> D[ជ្រើសរើសមុខវិជ្ជា]
    D --> E{រួចហើយឬ?}
    E -->|ទេ| F[បញ្ចូល course_offering_id]
    F --> G{មានឈ្មោះរួច?}
    G -->|ទេ| H[បង្កើត StudentCourseEnrollment]
    H --> I[បង្ហាញសារជោគជ័យ]
    G -->|បាទ/ចាស| J[បង្ហាញសារ "រួចហើយ"]
    E -->|បាទ/ចាស| K[បង្ហាញសារ "រួចហើយ"]
    
    B -->|ចុះឈ្មោះតាមកម្មវត្ថុ| L[មើលកម្មវត្ថុសិស្ស]
    L --> M[ជ្រើសរើសកម្មវត្ថុ]
    M --> N[ទាញយក CourseOffering សម្រាប់កម្មវត្ថុ]
    N --> O{មានមុខវិជ្ជាមិនទាន់ចុះឈ្មោះ?}
    O -->|បាទ/ចាស| P[ចុះឈ្មោះមុខវិជ្ជាទាំងអស់]
    P --> Q{រក្សាទុកជោគជ័យ?}
    Q -->|បាទ/ចាស| R[បង្ហាញ "ចុះឈ្មោះបាន X មុខវិជ្ជា"]
    Q -->|ទេ| S[បង្ហាញសារកំហុស]
    O -->|ទេ| T[បង្ហាញ "មិនមានមុខវិជ្ជាថ្មី"]
    
    I --> U([ចប់])
    J --> U
    K --> U
    R --> U
    S --> U
    T --> U
```

---

## FLOWCHART 4: QR Code Attendance (Student Scan)

```mermaid
flowchart TD
    A([សិស្សស្កែន QR / Start]) --> B[បើក Camera Scanner]
    B --> C{ស្កែន QR Code បាន?}
    C -->|ទេ| D[សូមស្កែនឡើងវិញ]
    D --> C
    
    C -->|បាទ/ចាស| E[ផ្ញើ token ទៅ server]
    E --> F{Token ត្រឹមត្រូវ?}
    F -->|ទេ| G[បង្ហាញ "QR Code មិនត្រឹមត្រូវ"]
    F -->|បាទ/ចាស| H{Token ផុតកំណត់?}
    H -->|បាទ/ចាស| I[បង្ហាញ "QR Code ផុតកំណត់"]
    H -->|ទេ| J{សិស្សមានឈ្មោះក្នុងថ្នាក់?}
    J -->|ទេ| K[បង្ហាញ "បងគ្មានឈ្មោះក្នុងថ្នាក់នេះ"]
    J -->|បាទ/ចាស| L{ស្កែនរួចរាល់ហើយ?}
    L -->|បាទ/ចាស| M[បង្ហាញ "បងបានស្កែនរួចរាល់ហើយ"]
    L -->|ទេ| N[បង្កើត AttendanceRecord]
    N --> O[status = present]
    O --> P[remarks = QR Scan]
    P --> Q[បង្ហាញ "វត្តមានត្រូវបានកត់ត្រា!"]
    
    G --> R([ចប់])
    I --> R
    K --> R
    M --> R
    Q --> R
```

---

## FLOWCHART 5: Class Leader Attendance

```mermaid
flowchart TD
    A([ប្រធានថ្នាក់ចូល / Start]) --> B{ជា Class Leader?}
    B -->|ទេ| C[Abort 403: មិនមែនជាប្រធានថ្នាក់]
    B -->|បាទ/ចាស| D[ទាញយកសិស្សក្នុងថ្នាក់]
    D --> E[បង្ហាញ Form វត្តមាន]
    E --> F[សិស្សជ្រើសរើសស្ថានភាពសម្រាប់សិស្សនីមួយៗ]
    F --> G{ស្ថានភាព?}
    G -->|present| H[វត្តមាន]
    G -->|absent| I[អវត្តមាន]
    G -->|late| J[យឺត]
    G -->|permission| K[សិទ្ធិ]
    
    H --> L[បញ្ចូល attendance array]
    I --> L
    J --> L
    K --> L
    
    L --> M[បញ្ជូន Form]
    M --> N{attendance_date មាន?}
    N -->|ទេ| O[បង្ហាញកំហុស validation]
    N -->|បាទ/ចាស| P[ចាប់ផ្តើម DB Transaction]
    P --> Q{គ្រប់សិស្ស?}
    Q -->|ទេ| R[ពិនិត្យ student_user_id]
    R --> S{ក្នុង enrollment?}
    S -->|ទេ| Q
    S -->|បាទ/ចាស| T{ស្ថានភាពត្រឹមត្រូវ?}
    T -->|ទេ| Q
    T -->|បាទ/ចាស| U[updateOrInsert AttendanceRecord]
    U --> V[remarks = Class Leader]
    V --> Q
    
    Q -->|បាទ/ចាស| W[Commit Transaction]
    W --> X[បង្ហាញសារជោគជ័យ]
    
    C --> Y([ចប់])
    O --> Y
    X --> Y
```

---

## FLOWCHART 6: Student View Grades

```mermaid
flowchart TD
    A([មើលពិន្ទុ / Start]) --> B[ទាញយក StudentCourseEnrollments]
    B --> C[ទាញយក ExamResults សម្រាប់មុខវិជ្ជាទាំងអស់]
    C --> D[ជ្រើសរើស Academic Year និង Semester]
    D --> E[-filter ពិន្ទុតាមឆ្នាំសិក្សា]
    E --> F{មានពិន្ទុ?}
    F -->|ទេ| G[បង្ហាញ "មិនទាន់មានពិន្ទុ"]
    F -->|បាទ/ចាស| H[គណនាពិន្ទុសម្រាប់មុខវិជ្ជានីមួយៗ]
    
    H --> I[ទាញយក Attendance Score]
    I --> J[បែងចែកពិន្ទុ: Assignment, Quiz, Exam]
    J --> K[គណនា Total Score]
    K --> L[ពិនិត្យ Failing Logic]
    L --> M{Final < 24 OR Midterm < 9 OR Assignment < 9 OR Attendance < 9?}
    M -->|បាទ/ចាស| N[Grade = F]
    M -->|ទេ| O[គណនា Letter Grade]
    
    N --> P[គណនា GPA]
    O --> P
    P --> Q[គណនាការឈ្នះរង្វាន់]
    Q --> R[បង្ហាញ ពិន្ទុសរុប, GPA, ឈ្មោះ, ចំណាត់ថ្នាក់]
    R --> S{សិស្សចង់មើល?}
    S -->|ពិន្ទុមុខវិជ្ជា| T[បង្ហាញពិន្ទីមុខវិជ្ជាលម្អិត]
    S -->|ការប្រឡង| U[បង្ហាញការប្រឡងទាំងអស់]
    S -->|Export| V[ទាញយក Excel]
    
    T --> W([ចប់])
    U --> W
    V --> W
    G --> W
```

---

## FLOWCHART 7: Student View Schedule

```mermaid
flowchart TD
    A([មើលកាលវិភាគ / Start]) --> B[ទាញយក StudentCourseEnrollments]
    B --> C[ទាញយក Schedules សម្រាប់មុខវិជ្ជាទាំងអស់]
    C --> D[រៀបចំតាមថ្ងៃ: Monday → Sunday]
    D --> E[រៀបចំតាមម៉ោងចាប់ផ្តើម]
    E --> F[ទាញយកព័ត៌មាន: Room, Lecturer, Course]
    F --> G[បង្ហាញកាលវិភាគ]
    G --> H{សិស្សចង់?}
    H -->|Print| I[បើក Print Dialog]
    H -->|Export Word| J[ទាញយក Word Document]
    H -->|ត្រឡប់| K[ត្រឡប់ Dashboard]
    
    I --> L([ចប់])
    J --> L
    K --> L
```

---

## FLOWCHART 8: Student Notifications

```mermaid
flowchart TD
    A([មើលការជូនដំណឹង / Start]) --> B[ទាញយក Announcements]
    B --> C[ទាញយក Notifications]
    C --> D[បញ្ចូលទិន្នន័យ]
    D --> E[រៀបចំតាមកាលបរិច្ឆេទថ្មីបំផុត]
    E --> F[បង្ហាញការជូនដំណឹង]
    F --> G{សិស្សជ្រើសរើស?}
    
    G -->|标记为已读| H[PATCH /notifications/{id}/read]
    H --> I[更新 read_at]
    I --> J[ត្រឡប់]
    
    G -->|标记 announcement ជា已读| K[PATCH /announcements/{id}/read]
    K --> L[បញ្ចូល NotificationRead]
    L --> J
    
    G -->|标记ទាំងអស់ជាបានអាន| M[PATCH /notifications/read-all]
    M --> N[ updateAll read_at = now]
    N --> J
    
    G -->|ត្រឡប់| J
    
    J --> O([ចប់])
```

---

## FLOWCHART 9: Student Profile Management

```mermaid
flowchart TD
    A([គ្រប់គ្រងព័ត៌មាន / Start]) --> B{សកម្មភាព?}
    
    B -->|មើលព័ត៌មាន| C[GET /student/profile]
    C --> D[ទាញយក User + Profile + StudentProfile]
    D --> E[បង្ហាញព័ត៌មាន]
    
    B -->|កែប្រែ| F[GET /student/profile/edit]
    F --> G[បង្ហាញ Form កែប្រែ]
    G --> H[សិស្សបំពេញទិន្នន័យ]
    H --> I{មានរូបភាពថ្មី?}
    I -->|បាទ/ចាស| J[បង្ហោះរូបភាពទៅ Cloudinary]
    I -->|ទេ| K[រក្សាទុករូបភាពចាស់]
    J --> L[PUT /student/profile]
    K --> L
    L --> M[រក្សាទុក User + Profile]
    M --> N[បង្ហាញសារជោគជ័យ]
    
    E --> O([ចប់])
    N --> O
```

---

## FLOWCHART 10: AI Chatbot Interaction

```mermaid
flowchart TD
    A([សិស្សបើក Chatbot / Start]) --> B[បើក Chat Window]
    B --> C[សិស្សផ្ញើសារ]
    C --> D[ទាញយក User Profile: Gender]
    D --> E{Gender?}
    E -->|ប្រុស| F[ប្រើប្រាស់ "បងប្រុស"]
    E -->|ស្រី| G[ប្រើប្រាស់ "បងស្រី"]
    E -->|ទេ| H[ប្រើប្រាស់ "អ្នកសិក្សា"]
    
    F --> I[ទាញយក Enrolled Courses]
    G --> I
    H --> I
    
    I --> J[ទាញយក Grade Data]
    J --> K[ទាញយក Schedule Data]
    K --> L[បង្កើត Context String]
    L --> M[ផ្ញើសារទៅ AI API]
    M --> N{AI Response?}
    N -->|ជោគជ័យ| O[បង្ហាញចម្លើយ]
    N -->|កំហុស| P[បង្ហាញសារកំហុស]
    
    O --> Q{សិស្សចង់បន្ត?}
    Q -->|បាទ/ចាស| C
    Q -->|ទេ| R[បិទ Chat Window]
    
    R --> S([ចប់])
    P --> S
```

---

## FLOWCHART 11: Complete Student Process Overview

```mermaid
flowchart TD
    A([សិស្សចូលប្រើប្រាស់ / Start]) --> B[Login]
    B --> C[Dashboard]
    
    C --> D{សកម្មភាព?}
    
    D -->|1. មុខវិជ្ជា| E[Enrolled Courses]
    E --> E1[មើលមុខវិជ្ជា]
    E --> E2[ចុះឈ្មោះមុខវិជ្ជា]
    E --> E3[មើលពិន្ទុ]
    
    D -->|2. វត្តមាន| F[Attendance]
    F --> F1[មើលវត្តមាន]
    F --> F2[ស្កែន QR]
    F --> F3[ប្រធានថ្នាក់កត់ត្រា]
    
    D -->|3. កាលវិភាគ| G[Schedule]
    G --> G1[មើលកាលវិភាគ]
    G --> G2[ Print/Export]
    
    D -->|4. ពិន្ទុ| H[Grades]
    H --> H1[មើលពិន្ទុសរុប]
    H --> H2[មើលពិន្ទីមុខវិជ្ជា]
    H --> H3[Export Excel]
    
    D -->|5. ការជូនដំណឹង| I[Notifications]
    I --> I1[មើលការប្រកាស]
    I --> I2[មើលការជូនដំណឹង]
    
    D -->|6. ព័ត៌មាន| J[Profile]
    J --> J1[មើលព័ត៌មាន]
    J --> J2[កែប្រែព័ត៌មាន]
    
    D -->|7. AI| K[Chatbot]
    K --> K1[សួរសំណួរ]
    K --> K2[មើលពិន្ទុ]
    K --> K3[មើលកាលវិភាគ]
    
    E1 --> L([ចប់])
    E2 --> L
    E3 --> L
    F1 --> L
    F2 --> L
    F3 --> L
    G1 --> L
    G2 --> L
    H1 --> L
    H2 --> L
    H3 --> L
    I1 --> L
    I2 --> L
    J1 --> L
    J2 --> L
    K1 --> L
    K2 --> L
    K3 --> L
```

---

DESIGN NOTES:
- Use Mermaid flowchart syntax (```mermaid)
- Diamond shapes for decisions: {question?}
- Rounded shapes for start/end: ([text])
- Rectangles for processes: [text]
- Arrows show flow direction
- Number each flowchart
- Include both Khmer and English labels
- Academic thesis style
- Color coding: Green for start/end, Blue for processes, Yellow for decisions
```

## How to Use

1. Copy the prompt above
2. Paste into ChatGPT, Claude, or Gemini
3. Ask for Mermaid diagram output
4. Render at https://mermaid.live or embed in Markdown

## Files Analyzed

- `routes/web.php` — Student routes (lines 364-401)
- `app/Http/Controllers/Student/StudentController.php` — Dashboard, profile
- `app/Http/Controllers/Student/StudentGradeController.php` — Grades, enrollment, schedule
- `app/Http/Controllers/Student/StudentAttendanceController.php` — Attendance, leader
- `app/Http/Controllers/AttendanceController.php` — QR scan process
- `app/Http/Controllers/Auth/QrLoginController.php` — QR login
