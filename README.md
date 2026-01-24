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

## System Screenshots

### Authentication
<p align="center">
  <img src="screenshot/login/signup/login.png" width="750"/>
  <br/>
  <em>Login page</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/signup.png" width="750"/>
  <br/>
  <em>User registration page</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/forgot-password.png" width="750"/>
  <br/>
  <em>Forgot password page</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/reset-password.png" width="750"/>
  <br/>
  <em>Password reset page</em>
</p>

---

### SuperAdmin Module
<p align="center">
  <img src="screenshot/superadmin/sa-dashboard.png" width="750"/>
  <br/>
  <em>SuperAdmin dashboard</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/manage-all-users.png" width="750"/>
  <br/>
  <em>User management</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/addnewuser.png" width="750"/>
  <br/>
  <em>Add new user</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/add-template.png" width="750"/>
  <br/>
  <em>Create content generation template</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/all-template.png" width="750"/>
  <br/>
  <em>Template library</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/document.png" width="750"/>
  <br/>
  <em>User-generated documents</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/document-cleanup.png" width="750"/>
  <br/>
  <em>Document cleanup</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/admin-activity.png" width="750"/>
  <br/>
  <em>Admin activity tracking</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/activity-log-cleanup.png" width="750"/>
  <br/>
  <em>Activity log cleanup</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/profile.png" width="750"/>
  <br/>
  <em>SuperAdmin profile</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/change-password.png" width="750"/>
  <br/>
  <em>Change password</em>
</p>

---

### Admin Module
<p align="center">
  <img src="screenshot/admin/dash.png" width="750"/>
  <br/>
  <em>Admin dashboard</em>
</p>

<p align="center">
  <img src="screenshot/admin/manage-user.png" width="750"/>
  <br/>
  <em>User deletion management</em>
</p>

<p align="center">
  <img src="screenshot/admin/add-template.png" width="750"/>
  <br/>
  <em>Create content generation template</em>
</p>

<p align="center">
  <img src="screenshot/admin/all-template.png" width="750"/>
  <br/>
  <em>Template library</em>
</p>

<p align="center">
  <img src="screenshot/admin/document.png" width="750"/>
  <br/>
  <em>Document history</em>
</p>

<p align="center">
  <img src="screenshot/admin/profile.png" width="750"/>
  <br/>
  <em>Admin profile</em>
</p>

<p align="center">
  <img src="screenshot/admin/change-password.png" width="750"/>
  <br/>
  <em>Change password</em>
</p>

---

### User Module (Lecturer / Student)
<p align="center">
  <img src="screenshot/user/dash.png" width="750"/>
  <br/>
  <em>User dashboard</em>
</p>

<p align="center">
  <img src="screenshot/user/template-provided.png" width="750"/>
  <br/>
  <em>Templates provided by administrators</em>
</p>

<p align="center">
  <img src="screenshot/user/content-generator.png" width="750"/>
  <br/>
  <em>AI-powered content generator</em>
</p>

<p align="center">
  <img src="screenshot/user/AI-suggestion-based-on-input.png" width="750"/>
  <br/>
  <em>AI suggestions based on user input</em>
</p>

<p align="center">
  <img src="screenshot/user/output.png" width="750"/>
  <br/>
  <em>Generated content output</em>
</p>

<p align="center">
  <img src="screenshot/user/document.png" width="750"/>
  <br/>
  <em>Document history</em>
</p>

<p align="center">
  <img src="screenshot/user/download-as-pdf.png" width="750"/>
  <br/>
  <em>Download as PDF</em>
</p>

<p align="center">
  <img src="screenshot/user/profile.png" width="750"/>
  <br/>
  <em>User profile</em>
</p>

<p align="center">
  <img src="screenshot/user/change-pass.png" width="750"/>
  <br/>
  <em>Change password</em>
</p>




   


