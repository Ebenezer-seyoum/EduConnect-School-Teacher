# EduConnect — School and Teacher Connection System

EduConnect is a web-based system that connects schools and teachers in one organized digital environment.

The system helps teachers discover school vacancies and allows schools to publish teaching opportunities. It also includes teacher profiles, vacancy details, user accounts, application requests, administration tools, and email-based account support.

## Key Features

- 🎓 **Teacher Discovery**  
  Allows users to search for teachers and view detailed teacher profiles.

- 🏫 **School Vacancy Management**  
  Enables schools to publish and manage available teaching positions.

- 📋 **Vacancy Details**  
  Displays school information, job requirements, and opportunity details.

- 📩 **Vacancy Requests**  
  Allows teachers to respond to suitable school opportunities.

- 👤 **User Authentication**  
  Includes registration, login, email verification, password reset, and account recovery.

- 🛠️ **Administrative Management**  
  Supports the management of users, teachers, schools, vacancies, and system activities.

- 📧 **Email Communication**  
  Uses PHPMailer for verification codes, password recovery, and account notifications.

- 📱 **Responsive Interface**  
  Built with Bootstrap for access across desktop, tablet, and mobile devices.

## Main Areas

- Home and public information pages
- Teacher search and profile details
- School vacancy listings
- Vacancy detail pages
- Teacher request workflow
- User registration and login
- Password recovery and email verification
- Admin dashboard
- Teacher dashboard
- Database connection and application logic

## Technologies

- PHP
- JavaScript
- Bootstrap
- PHPMailer
- Database-driven application architecture

## Project Structure

```text
EduConnect-School-Teacher/
├── Home/          # Home and public pages
├── admin/         # Administrative features
├── teacher/       # Teacher-related features
├── login/         # Login and account functionality
├── connection/    # Database connection and configuration
├── PHPMailer/     # Email communication support
├── index.php      # Main entry point
├── find-teachers.php
├── teacher_detail.php
├── school-vacancy.php
├── vacancy_detail.php
└── vacancy_request.php
