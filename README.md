
```
bcs-placement-portal
├─ app
│  ├─ controllers
│  │  ├─ AdminController.php
│  │  ├─ AuthController.php
│  │  ├─ EmployerController.php
│  │  ├─ HomeController.php
│  │  ├─ MatchController.php
│  │  ├─ MessageController.php
│  │  ├─ PlacementController.php
│  │  ├─ SfiaController.php
│  │  └─ StudentController.php
│  ├─ core
│  │  ├─ App.php
│  │  ├─ Auth.php
│  │  ├─ Controller.php
│  │  ├─ Database.php
│  │  ├─ Model.php
│  │  └─ Session.php
│  ├─ models
│  │  ├─ Application.php
│  │  ├─ Employer.php
│  │  ├─ MatchModel.php
│  │  ├─ Message.php
│  │  ├─ Placement.php
│  │  ├─ Student.php
│  │  └─ User.php
│  └─ views
│     ├─ admin
│     │  └─ dashboard.php
│     ├─ auth
│     │  ├─ login.php
│     │  ├─ register_career.php
│     │  ├─ register_employer.php
│     │  └─ register_student.php
│     ├─ employer
│     │  ├─ applicants.php
│     │  ├─ create_placement.php
│     │  ├─ dashboard.php
│     │  ├─ edit_placement.php
│     │  ├─ placements.php
│     │  └─ profile.php
│     ├─ home.php
│     ├─ layouts
│     │  ├─ footer.php
│     │  ├─ header.php
│     │  └─ navbar.php
│     ├─ match
│     │  └─ overview.php
│     ├─ message
│     │  ├─ chat.php
│     │  ├─ inbox.php
│     │  └─ view.php
│     ├─ pages
│     │  └─ benefits.php
│     ├─ placement
│     │  └─ index.php
│     ├─ sfia
│     │  └─ index.php
│     └─ student
│        ├─ dashboard.php
│        ├─ matches.php
│        ├─ profile.php
│        └─ upload_cv.php
├─ config
│  ├─ app.php
│  └─ database.php
├─ database
│  ├─ bcs-placement-portal.sqlite
│  └─ migrations
│     ├─ 001_create_users.sql
│     ├─ 002_create_students.sql
│     ├─ 003_create_employers.sql
│     ├─ 004_create_placements.sql
│     └─ 005_create_matches.sql
├─ public
│  ├─ .htaccess
│  ├─ assets
│  │  ├─ css
│  │  │  └─ style.css
│  │  ├─ images
│  │  │  ├─ pexels-fauxels-3184306.jpg
│  │  │  ├─ pexels-fauxels-3184423.jpg
│  │  │  ├─ pexels-googledeepmind-17483848.jpg
│  │  │  └─ pexels-googledeepmind-17483850.jpg
│  │  └─ js
│  │     └─ app.js
│  ├─ index.php
│  └─ uploads
└─ storage
   └─ cv
      └─ student_1_1764851624.pdf

```