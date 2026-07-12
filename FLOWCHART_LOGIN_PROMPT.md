# Prompt: Generate Login Flowchart
## National Meanchey University — Class Management System

Copy the prompt below and paste into ChatGPT / Claude / Gemini.

---

## BEGIN PROMPT ————————————————————————————

Create a **Login Process Flowchart** for a **Class Management System** at National Meanchey University. Use **Mermaid flowchart TD** syntax.

Use Khmer text for the title and English for the flowchart nodes. Style it like an academic thesis diagram with light blue filled nodes.

---

### TITLE (Khmer):
```
៨.១.២ រាល់ដំណើរការនៃការចូលប្រើប្រាស់ប្រព័ន្ធ៖ Flowchart
ច. វិធីសាស្រ្តសម្រាប់ការចូលប្រើប្រព័ន្ធដោយប្រើការ Log in
```

### SUBTITLE:
```
ប្រភេទ ទី ១.៣ ៖ Flowchart Log in
```

---

### FLOWCHART LOGIC (based on this project's actual code):

```
Start
  ↓
Has logged in? (Check session)
  ├── Yes → Dashboard → Stop
  └── No → Login Form (Email/Student ID + Password)
               ↓
         Authenticate Email & Password
           ├── No → Back to Login Form (show error: "ពាក្យសម្ងាត់មិនត្រឹមត្រូវ")
           └── Yes → Authorize Permissions (Check role)
                      ├── As Admin → Admin Dashboard → Stop
                      ├── As Professor → Professor Dashboard → Stop
                      └── As Student → Student Dashboard → Stop
```

---

### DETAILED FLOWCHART NODES:

1. **Start** (Oval)
2. **Has logged in?** (Diamond) — Check if user session exists
   - Yes → **Dashboard** (Rectangle) → **Stop** (Oval)
   - No → **Login Form** (Rectangle) — Fields: Email & Password
3. **Authenticate Email & Password** (Diamond) — Validate credentials via `LoginRequest`
   - No → Back to **Login Form**
   - Yes → **Authorize Permissions** (Diamond) — Check `role` column
4. **Authorize Permissions** (Diamond) — Three branches:
   - As **Admin** → **Admin Dashboard** (Rectangle) → **Stop**
   - As **Professor** → **Professor Dashboard** (Rectangle) → **Stop**
   - As **Student** → **Student Dashboard** (Rectangle) → **Stop**

---

### VISUAL STYLE:

- Use `flowchart TD` (top-down)
- Start/Stop nodes: **Oval** shape, filled with light blue (`#B0C4DE`)
- Process nodes (Login Form, Dashboards): **Rectangle**, filled light blue
- Decision nodes (Has logged in?, Authenticate, Authorize): **Diamond**, filled light blue
- All nodes have black text
- Arrows are black with labels (Yes/No, role names)
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

## What the Diagram Will Look Like

```
                    ┌─────────┐
                    │  Start   │
                    └────┬─────┘
                         │
                    ┌────┴─────┐
                    │Has logged│
                    │   in?    │
                    └──┬───┬───┘
                Yes    │   │   No
            ┌──────────┘   └──────────┐
            │                         │
      ┌─────┴─────┐           ┌───────┴───────┐
      │ Dashboard  │           │  Login Form   │
      └─────┬─────┘           │ Email & Pass  │
            │                  └───────┬───────┘
            │                          │
      ┌─────┴─────┐           ┌───────┴───────┐
      │   Stop    │           │ Authenticate  │
      └───────────┘           │ Email & Pass  │
                              └──┬────────┬───┘
                          No     │        │  Yes
                    ┌────────────┘        └──────────┐
                    │                                │
              ┌─────┴─────┐                  ┌───────┴───────┐
              │ Back to   │                  │  Authorize    │
              │Login Form │                  │  Permissions  │
              └───────────┘                  └──┬─────┬─────┬─┘
                                          Admin │     │Prof │Student
                                    ┌───────────┘     │     └──────────┐
                                    │                 │                │
                              ┌─────┴─────┐   ┌──────┴──────┐  ┌──────┴──────┐
                              │  Admin    │   │ Professor   │  │  Student    │
                              │Dashboard  │   │ Dashboard   │  │ Dashboard   │
                              └─────┬─────┘   └──────┬──────┘  └──────┬──────┘
                                    │                 │                │
                                    └────────┬────────┴────────┬───────┘
                                             │                 │
                                        ┌────┴────┐      ┌────┴────┐
                                        │  Stop   │      │  Stop   │
                                        └─────────┘      └─────────┘
```
