# UPTM Academic AI Assistant System

## Overview

The UPTM Academic AI Assistant System is a web-based academic support platform for students and lecturers. It uses AI-powered templates to help with academic writing and learning tasks, with access controlled by role-based authentication.

The system is built with Laravel 12, MySQL, and the OpenAI API. Dashboards use the Softnio NioBoard UI (Bootstrap-based). Tailwind CSS is used on the authentication pages.

## Objectives

- Provide AI-assisted academic writing support for students and lecturers
- Implement secure authentication and role-based access control
- Allow SuperAdmin to manage users, templates, activity logs, and document retention
- Allow Admin to manage templates and student/lecturer accounts
- Keep a document history of AI-generated outputs per user

## Technologies Used

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Softnio NioBoard (Bootstrap) for dashboards; Tailwind CSS for login, register, and password reset; JavaScript (Fetch API)
- **Database:** MySQL
- **AI Integration:** OpenAI PHP SDK (`openai-php/laravel`)
- **Authentication:** Laravel Breeze with role middleware (`superadmin`, `admin`, `student`, `lecturer`)

## User Roles & Features

There are four roles. Students and lecturers share the same user portal. Templates are filtered by category (`student` or `lecturer`).

### SuperAdmin

- Create, view, update, and delete Admin, Lecturer, and Student accounts (soft delete)
- Activate or deactivate user accounts
- Search users and send password-reset emails
- Create and manage AI templates (CRUD and active/inactive status)
- Generate content from templates and keep own document history
- View Admin activity logs, export them as CSV, and clean up old logs
- Configure activity-log and document retention, including manual cleanup of old generated documents

### Admin

- View and delete Lecturer and Student accounts only (cannot create users, activate accounts, or delete other Admins)
- Create and manage AI templates (CRUD and status toggle)
- Generate content from templates and manage own document history
- Update profile and password
- No access to system activity logs or retention settings

### Users (Lecturers / Students)

- Register for a Lecturer or Student account
- Use active templates that match their role category
- Generate academic content and request AI suggestions for input fields
- View, edit, and delete own document history
- Update profile and password
- Inactive accounts cannot sign in

### Shared Features (All Roles)

- User profile management
- Change password
- Document history for that user's AI-generated outputs (view, edit, delete)
- SuperAdmin and Admin can also generate content, not only Lecturers and Students

## Content Generation Workflow

### 1. Template-based input

Each template defines:

- Page title and description
- Dynamic input fields
- Validation rules for those fields

Input fields are rendered from the selected template. Users can also choose:

- Language: English or Bahasa Melayu
- AI model: GPT-3.5 Turbo or GPT-4

### 2. User input validation

Before the request is sent:

- Required fields are checked in the browser
- Empty inputs are highlighted
- The generate button is disabled while the request is in progress

### 3. Data submission

When the user clicks Generate Content:

- Form data is sent with the Fetch API
- A POST request goes to the Laravel backend
- CSRF protection is applied

### 4. AI processing (OpenAI API)

On the backend:

- The controller validates the request
- User input is inserted into the template prompt
- The selected model (`gpt-3.5-turbo` or `gpt-4`) is called through the OpenAI API
- The response is returned to the frontend as JSON

### 5. Output display

After content is generated:

- The AI output is cleaned and formatted in JavaScript
- The content is shown in the output panel
- The result can be saved to document history

## AI Suggestions for Input Fields

### How it works

1. **User input**  
   The user types into a textarea and clicks Get AI Suggestions.

2. **AJAX request**  
   JavaScript sends the current input, language, and template context to Laravel.

3. **Backend processing**  
   The controller validates the input and builds a structured prompt. It asks OpenAI for 6 academic suggestions mapped to Bloom's Taxonomy:

   - Remember
   - Understand
   - Apply
   - Analyze
   - Evaluate
   - Create

   Suggestion requests use GPT-4 (the generation form still lets the user pick GPT-3.5 Turbo or GPT-4 for full content).

4. **OpenAI response**  
   Suggestions are returned as JSON.

5. **Display**  
   JavaScript shows the suggestions in a dropdown under the textarea, with Bloom's level labels. The user can select a suggestion to refine the input before generating full content.

  
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
   
---

## Swimlane Diagram

<p align="center">
  <img src="screenshot/swimlane.png" width="750"/>
  <br/>
</p>

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




   


