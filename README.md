# UPTM Academic AI Assistant System

## Overview

The UPTM Academic AI Assistant System is a web-based academic support platform for students and lecturers. It uses AI-powered templates to help with academic writing and learning tasks, with access controlled by role-based authentication.

The system is built with Laravel 12, MySQL, and the OpenAI API. Dashboards use the Softnio NioBoard UI (Bootstrap-based). Tailwind CSS is used on the authentication pages.

The application calls existing OpenAI models. It does not train a model. Text calls use the OpenAI PHP SDK (`openai-php/laravel`). A photo or PDF is sent with a direct Chat Completions request so the file can be attached. The key is `OPENAI_API_KEY` in `.env`, read by `config/openai.php`.

## Objectives

- Provide AI-assisted academic writing support for students and lecturers
- Implement secure authentication and role-based access control
- Allow SuperAdmin to manage users, templates, activity logs, document retention, user reminders, and a weekly operations brief
- Allow Admin to manage templates and student/lecturer accounts
- Keep a document history of AI-generated outputs per user
- Help authors build templates (suggest a new template, find an existing one, and write the custom prompt)

## Technologies Used

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Softnio NioBoard (Bootstrap) for dashboards; Tailwind CSS for login, register, and password reset; JavaScript (Fetch API and jQuery)
- **Database:** MySQL
- **AI Integration:** OpenAI PHP SDK (`openai-php/laravel`) for text; direct HTTP for image and PDF generation
- **Authentication:** Laravel Breeze with role middleware (`superadmin`, `admin`, `student`, `lecturer`)
- **Mail:** Laravel mail, used by user reminders

## User Roles & Features

There are four roles. Students and lecturers share the same user portal. Templates are filtered by category (`Student` or `Lecturer`).

### SuperAdmin

- Create, view, update, and delete Admin, Lecturer, and Student accounts (soft delete)
- Activate or deactivate user accounts
- Search users and send password-reset emails
- Create and manage AI templates (CRUD and active/inactive status)
- Choose whether a template is type-only or allows an image or PDF upload
- Suggest new templates, find an existing template, and write a custom prompt with AI
- Generate content from templates, including from an uploaded brief, and keep own document history
- View Admin activity logs, export them as CSV, and clean up old logs
- Configure activity-log and document retention, including manual cleanup of old generated documents
- Send reminder emails to students and lecturers who have not generated a document recently
- Read the weekly AI operations brief on the dashboard

### Admin

- View and delete Lecturer and Student accounts only (cannot create users, activate accounts, or delete other Admins)
- Create and manage AI templates (CRUD and status toggle), including the brief-upload option
- Suggest new templates, find an existing template, and write a custom prompt with AI
- Generate content from templates, including from an uploaded brief, and manage own document history
- Update profile and password
- No access to system activity logs, retention settings, user reminders, or the operations brief

### Users (Lecturers / Students)

- Register for a Lecturer or Student account
- Use active templates that match their role category
- Find a template by describing what they need
- Generate academic content, upload an assignment brief when the template allows it, and request AI suggestions for input fields
- View, edit, and delete own document history, and download a document as PDF
- Update profile and password
- Inactive accounts cannot sign in

### Shared Features (All Roles)

- User profile management
- Change password
- Document history for that user's AI-generated outputs (view, edit, delete)
- SuperAdmin and Admin can also generate content, not only Lecturers and Students

## Models used

The same API key can call more than one model. OpenAI charges a different price per token for each model. This system picks the model in code.

| Feature | Model sent to OpenAI |
| --- | --- |
| Generate content from typed fields | `gpt-4` or `gpt-3.5-turbo`, chosen on the form |
| Generate content from a photo or PDF | Form choice `gpt-4` becomes `gpt-4o`. Form choice `gpt-3.5-turbo` becomes `gpt-4o-mini` |
| Field suggestions | `gpt-4` |
| Suggest new templates | `gpt-4` |
| Find a template | `gpt-4o-mini` |
| Write custom prompt | `gpt-4o-mini` |
| User reminder email | `gpt-3.5-turbo` |
| Weekly operations brief | `gpt-4` |

`gpt-4` and `gpt-3.5-turbo` cannot read images. Brief upload therefore switches to a vision-capable model.

## Content Generation Workflow

### 1. Template-based input

Each template defines:

- Page title and description
- Dynamic input fields
- Whether users type only, or may upload an image or PDF
- A custom prompt that contains `{variable}` placeholders matching the field titles

Input fields are rendered from the selected template. Users can also choose:

- Language: English or Bahasa Melayu
- AI model: GPT-3.5 Turbo or GPT-4

### 2. User input validation

Before the request is sent:

- Required fields are checked in the browser
- Empty inputs are highlighted
- If the template allows a brief upload, a JPG, PNG, WEBP, or PDF is required (max 8 MB). Typed fields become optional
- The generate button is disabled while the request is in progress

### 3. Data submission

When the user clicks Generate Content:

- Form data is sent with the Fetch API
- A POST request goes to the Laravel backend
- CSRF protection is applied
- The client waits up to 120 seconds when a file is attached

### 4. AI processing (OpenAI API)

On the backend:

- The controller validates the request
- User input is inserted into the template prompt. Both `{course_name}` and `{course name}` match a field whose title is `course name`
- A system message forces the selected language
- **Type only:** the selected model (`gpt-3.5-turbo` or `gpt-4`) is called through the OpenAI PHP SDK
- **Brief upload:** empty fields become “Take this from the attached assignment brief.” `AssignmentBriefExtractor` calls the API over HTTP, attaches the file, and uses `gpt-4o` or `gpt-4o-mini`. A PDF is sent as base64. An image is sent as a data URL. The file is not stored. The PHP time limit is 120 seconds and the HTTP timeout is 110 seconds
- The response is returned to the frontend as JSON. A failed file read returns HTTP 422

### 5. Output display

After content is generated:

- The AI output is cleaned and formatted in JavaScript
- The content is shown in the output panel
- The result is saved to document history, including the original brief filename when a file was used

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

   Suggestion requests use GPT-4. Bahasa Melayu uses the Malay level names. The generation form still lets the user pick GPT-3.5 Turbo or GPT-4 for the full document.

4. **OpenAI response**  
   Suggestions are returned as JSON. Each line ends with a level label such as `[LEVEL: REMEMBER]`.

5. **Display**  
   JavaScript shows the suggestions in a dropdown under the textarea, with Bloom's level labels. The user can select a suggestion to refine the input before generating full content.

## Other AI features

### Suggest new templates

On Add Template, Admin and SuperAdmin open **Suggest templates**. The service loads up to 40 existing titles and asks GPT-4 for four new templates that fill gaps. Each card shows the title, category, description, and a **Reason**. **Use this template** fills the form. The author can still edit before saving. The model must not copy an existing title, and the prompt must include the `{variable}` for the one input field.

### Find a template

On the template library, any role can describe what they need (3–500 characters). GPT-4o-mini may return up to three existing templates and a reason for each. Students and lecturers only see active templates in their own category. Admins and superadmins can match any template, up to 40. Ids that are not in that list are discarded. The result links to that role's template page.

### Write a custom prompt

On Add Template, **Write with AI** opens a modal beside Custom Prompt Code. The author describes how the prompt should work. GPT-4o-mini writes the prompt and must keep every field variable, such as `{topic}`. If the model omits one, the server appends it. The **Copy** button next to a field title only copies `{variable}` to the clipboard. It does not call the model.

### User reminders

SuperAdmin sets how many days count as idle. Every Monday at 09:00, if the setting is on, the system emails up to 10 active students or lecturers who have not generated a document in that window and have not already been reminded in that window. GPT-3.5-turbo writes the subject and body. If that call fails, a fixed fallback sentence is sent. A row is stored for each attempt. Clearing a row lets that user be included again. It does not unsend the email. Sending to one chosen user skips the “already reminded” check.

### Weekly operations brief

Every Monday at 08:00, if the setting is on, the system counts the last seven days (documents, word count, active users, new users, new templates, admin activities, top templates, and documents by role). GPT-4 writes 5 to 7 bullets from those numbers only. If OpenAI fails, a numbers-only summary is stored instead. The latest brief is shown on the SuperAdmin dashboard. A brief already created in the last six days skips another scheduled run.

## Scheduled jobs

`routes/console.php` and `app/Console/Kernel.php` both register these jobs. The server must run `php artisan schedule:work`, or a cron that calls `php artisan schedule:run` every minute.

| Command | When | Calls OpenAI |
| --- | --- | --- |
| `ops:weekly-brief` | Monday 08:00 | Yes |
| `users:inactive-nudge` | Monday 09:00 | Yes |
| `activity:cleanup` | Daily 00:00 | No |
| `documents:cleanup` | Daily 00:30 | No |

Cleanup deletes old records. It does not call the model. Each AI job runs only when its setting is on.

## Installation (Local Setup)

1. Clone the repository:

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Configure environment:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set up the database and migrate:

   ```bash
   php artisan migrate
   ```

5. Add your OpenAI API key to `.env`:

   ```bash
   OPENAI_API_KEY=api_key_here
   ```

6. Run the application:

   ```bash
   php artisan serve
   ```

## Swimlane Diagram

<p align="center">
  <img src="screenshot/swimlane.png" width="750"/>
  <br/>
  <em>Old photo</em>
</p>

## System Screenshots

The images below are old photos. Not show the latest screens (brief upload, find a template, suggested-template reasons, write-with-AI, or user reminders).

### Authentication

<p align="center">
  <img src="screenshot/login/signup/login.png" width="750"/>
  <br/>
  <em>Login page — old photo</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/signup.png" width="750"/>
  <br/>
  <em>User registration page — old photo</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/forgot-password.png" width="750"/>
  <br/>
  <em>Forgot password page — old photo</em>
</p>

<p align="center">
  <img src="screenshot/login/signup/reset-password.png" width="750"/>
  <br/>
  <em>Password reset page — old photo</em>
</p>

### SuperAdmin Module

<p align="center">
  <img src="screenshot/superadmin/sa-dashboard.png" width="750"/>
  <br/>
  <em>SuperAdmin dashboard — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/manage-all-users.png" width="750"/>
  <br/>
  <em>User management — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/addnewuser.png" width="750"/>
  <br/>
  <em>Add new user — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/add-template.png" width="750"/>
  <br/>
  <em>Create content generation template — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/all-template.png" width="750"/>
  <br/>
  <em>Template library — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/document.png" width="750"/>
  <br/>
  <em>User-generated documents — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/document-cleanup.png" width="750"/>
  <br/>
  <em>Document cleanup — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/admin-activity.png" width="750"/>
  <br/>
  <em>Admin activity tracking — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/activity-log-cleanup.png" width="750"/>
  <br/>
  <em>Activity log cleanup — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/profile.png" width="750"/>
  <br/>
  <em>SuperAdmin profile — old photo</em>
</p>

<p align="center">
  <img src="screenshot/superadmin/change-password.png" width="750"/>
  <br/>
  <em>Change password — old photo</em>
</p>

### Admin Module

<p align="center">
  <img src="screenshot/admin/dash.png" width="750"/>
  <br/>
  <em>Admin dashboard — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/manage-user.png" width="750"/>
  <br/>
  <em>User deletion management — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/add-template.png" width="750"/>
  <br/>
  <em>Create content generation template — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/all-template.png" width="750"/>
  <br/>
  <em>Template library — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/document.png" width="750"/>
  <br/>
  <em>Document history — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/profile.png" width="750"/>
  <br/>
  <em>Admin profile — old photo</em>
</p>

<p align="center">
  <img src="screenshot/admin/change-password.png" width="750"/>
  <br/>
  <em>Change password — old photo</em>
</p>

### User Module (Lecturer / Student)

<p align="center">
  <img src="screenshot/user/dash.png" width="750"/>
  <br/>
  <em>User dashboard — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/template-provided.png" width="750"/>
  <br/>
  <em>Templates provided by administrators — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/content-generator.png" width="750"/>
  <br/>
  <em>AI-powered content generator — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/AI-suggestion-based-on-input.png" width="750"/>
  <br/>
  <em>AI suggestions based on user input — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/output.png" width="750"/>
  <br/>
  <em>Generated content output — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/document.png" width="750"/>
  <br/>
  <em>Document history — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/download-as-pdf.png" width="750"/>
  <br/>
  <em>Download as PDF — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/profile.png" width="750"/>
  <br/>
  <em>User profile — old photo</em>
</p>

<p align="center">
  <img src="screenshot/user/change-pass.png" width="750"/>
  <br/>
  <em>Change password — old photo</em>
</p>
