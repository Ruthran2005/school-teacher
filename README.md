# School ERP - Teacher app (standalone)

Same look and feel as the Admin app, in its own colour family (emerald green), so each portal is easy to tell apart.

## Install
```bash
unzip school-teacher.zip && cd school-teacher
bash setup.sh
php artisan serve --port=8001
```
Open http://127.0.0.1:8001 - login `teacher@school.test` / `password` (demo data is seeded).

## What you get
- **Dashboard** - classes, students, today's periods, attendance status, upcoming exams
- **Timetable** - your weekly teaching schedule
- **My students** - students of your classes with attendance %
- **Attendance** - mark attendance for your classes
- **Marks** - enter marks for your classes and subjects
- **Exams** - exam list and student report cards
- **Homework** - assign homework (with optional file), see who submitted, approve or send back for redo with remarks

"Your classes" and "your subjects" come from the timetable: a teacher sees a class once the admin has added them to that class in the Timetable. Teachers can only read and write data for their own classes (enforced on the server).

## Homework files
Homework attachments and submissions are stored inside the database (base64, max 5 MB each) so the Teacher and Student apps can both read them. If an upload fails, raise `upload_max_filesize` and `post_max_size` in `php.ini` to at least 6M.

## Database
Default: its own SQLite file. For real use point all three apps at one database (see the Admin README) so the admin's changes show up here: `DB_CONNECTION=mysql DB_DATABASE=school_erp DB_USERNAME=root DB_PASSWORD= bash setup.sh`.

## Changing colours
All colours live in `resources/css/app.css` (`:root` variables `--accent`, `--accent-dark`, `--accent-soft`, `--accent-ink`, plus the sidebar gradient). Dashboard card tints use Tailwind classes such as `bg-emerald-50 text-emerald-600` inside `resources/views`. After editing run `npm install && npm run build`. (`setup.sh` runs this automatically the first time, because `public/build` is not shipped. For a Vercel demo, build once and commit `public/build`, the same as the Admin app.)

Demo data is fake. Do not reuse the demo passwords for real accounts.
