# Student Admission Portal — Q1, Q2 and Q3

This pack contains three separate implementations based on the exam questions:
- `Q1_HTML_CSS_JS`: registration form with client-side validation
- `Q2_PHP_MySQL`: student CRUD, safe file uploads, and file metadata
- `Q3_Java_Servlet_JSP`: JDBC student listing and protected file streaming

## Requirements
- VS Code (or another editor)
- XAMPP (Apache + PHP + MySQL/MariaDB) for Q1 and Q2
- JDK 17+ and Maven for Q3
- Apache Tomcat 10.1+ for Q3 (Jakarta Servlet API)

## Q1 — Run the registration form
1. Open `Q1_HTML_CSS_JS` in VS Code.
2. Open `index.html` in a browser. You can use VS Code Live Server if installed.
3. Submit with missing or invalid values to test the validation.
4. This is a front-end-only demonstration; it does not save records to a database.

## Q2 — Run PHP/MySQL application with XAMPP
1. Install and open XAMPP. Start **Apache** and **MySQL**.
2. Copy the `Q2_PHP_MySQL` folder to `C:\xampp\htdocs\student-portal-q2`.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `Q2_PHP_MySQL/schema.sql` (Import tab), or paste its SQL into the SQL tab.
5. Confirm database settings in `config.php` (default XAMPP: host `localhost`, user `root`, blank password).
6. Open `http://localhost/student-portal-q2/`.
7. Add a student, then use Edit/View/Delete. Use small JPG/PNG/PDF files for upload.
8. Uploaded files are stored under `uploads/`; only metadata is stored in MySQL.

Security note: For a real deployment, configure a non-root database user, HTTPS, authentication, CSRF protection, stricter authorization, malware scanning, and backups. This is a classroom starter.

## Q3 — Run Java Servlet/JSP application
1. Install JDK 17+ and Maven. Set `JAVA_HOME` and make sure `java -version` and `mvn -version` work.
2. Install Tomcat 10.1+.
3. Create the database and tables by running `Q2_PHP_MySQL/schema.sql` in phpMyAdmin (or MySQL).
4. Edit `Q3_Java_Servlet_JSP/src/main/resources/db.properties` to match your MySQL credentials.
5. In a terminal, go to `Q3_Java_Servlet_JSP` and run:
   `mvn clean package`
6. Copy `target/student-portal-q3.war` into Tomcat's `webapps` folder.
7. Start Tomcat. Open `http://localhost:8080/student-portal-q3/login.jsp`.
8. Demo login: username `student`, password `student123`. This is hard-coded for classroom demonstration only.
9. To test file streaming, create a student and upload a file through Q2, then visit the Q3 student list. Q3 uses the `file_path` stored in MySQL; ensure the path points to the same shared uploads folder and edit `FileServlet.java`'s `UPLOAD_ROOT` accordingly.

### Important for Q3 file paths
Q2's default upload path is `Q2_PHP_MySQL/uploads`. Q3 does not automatically know your Windows folder path. In `FileServlet.java`, set `UPLOAD_ROOT` to the absolute path of that uploads folder, for example:
`C:/xampp/htdocs/student-portal-q2/uploads`
Use forward slashes. Only files inside this folder will be served.

## Database tables
`students` stores student details. `student_files` stores the saved relative path, file type, upload date and linked student ID.
