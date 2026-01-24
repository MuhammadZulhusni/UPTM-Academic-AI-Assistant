# UPTM Academic AI Assistant System

## Overview
The **UPTM Academic AI Assistant System** is a web-based academic support platform designed to simplify learning, teaching, and academic writing for **students and lecturers**.  
The system integrates **AI-powered content generation** to assist users with academic tasks while maintaining structured access control through multiple user roles.

This project was developed using **Laravel**, **Tailwind CSS**, **Bootstrap**, and the **OpenAI API**, focusing on security, usability, and role-based functionality.

---

## Objectives
- Provide AI-assisted academic support for students and lecturers  
- Implement secure authentication and role-based access control  
- Allow administrators to manage users, templates, and system activities  
- Maintain document history for academic reference  

---

## Technologies Used
- **Backend:** Laravel (PHP)
- **Frontend:** Tailwind CSS, Bootstrap
- **Database:** MySQL
- **AI Integration:** OpenAI API
- **Authentication:** Role-Based Access Control (RBAC)

---

## User Roles & Features

### SuperAdmin
SuperAdmin has full system control, including:
- Manage all users (**Admins, Lecturers, Students**) = CRUD
- Activate / deactivate user accounts
- Create new user accounts
- Create and manage AI content templates
- Template Library management (CRUD & status control)
- Track admin activities
- Clean up old admin activity logs
- Delete old user-generated documents to optimize database storage

---

### Admin
Admin has limited administrative privileges:
- Delete users (Admins, Lecturers, Students)
- Create AI content templates
- Manage Template Library (CRUD & status control)

---

### Users (Lecturers / Students)
Users can:
- Use AI templates provided by SuperAdmin or Admin
- Generate academic-related content
- View document history for past AI-generated outputs

---

## Shared Features (All Roles)
- User profile management
- Change password
- Document history (AI-generated output records)

---

## Security Features
- Secure authentication system
- Role-based access control
- Account status management (Active / Inactive)
- Activity logging for admin actions

---

## Installation (Local Setup)
1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/your-repository-name.git

2. Install dependencies:
   ```bash
   composer install
   npm install

3. Configure environment:
   ```bash
    cp .env.example .env
    php artisan key:generate

4. Set up database and migrate:
   ```bash
   php artisan migrate

5. Add your OpenAI API key to .env:
   ```bash
   OPENAI_API_KEY=your_api_key_here

6. Run the application:
   ```bash
   php artisan serve
   ```




   


