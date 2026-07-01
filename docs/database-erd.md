# Database ERD

This ERD shows the main application tables and how the Laravel models relate to each other.

```mermaid
erDiagram
    users {
        uuid id PK
        string name
        string email UK
        string role
        timestamp email_verified_at
        string password
        timestamps timestamps
    }

    user_preferences {
        uuid id PK
        uuid user_id FK,UK
        json peak_hours
        json avoid_hours
        smallint max_daily_tasks
        smallint focus_block_minutes
        smallint break_minutes
        boolean notifications_on
        timestamps timestamps
    }

    categories {
        uuid id PK
        uuid user_id FK
        string name
        string color
        string icon
        timestamps timestamps
    }

    tasks {
        uuid id PK
        uuid user_id FK
        uuid category_id FK
        uuid parent_task_id FK
        string title
        text description
        enum status
        enum priority
        integer estimated_minutes
        integer actual_minutes
        timestamp due_at
        timestamp started_at
        timestamp completed_at
        smallint ai_rank
        float ai_score
        json ai_reasoning
        timestamps timestamps
    }

    child_profiles {
        uuid id PK
        uuid parent_id FK
        string name
        date birthdate
        string avatar_color
        boolean audio_guidance_enabled
        timestamps timestamps
    }

    learning_activities {
        uuid id PK
        uuid parent_id FK
        string title
        string domain
        text prompt
        string audio_prompt
        tinyint age_min
        tinyint age_max
        string button_color
        boolean is_active
        timestamps timestamps
    }

    child_progress {
        uuid id PK
        uuid child_profile_id FK,UK
        uuid learning_activity_id FK,UK
        string status
        integer attempts
        timestamp completed_at
        timestamps timestamps
    }

    worksheets {
        uuid id PK
        uuid uploaded_by FK
        string title
        string subject
        string age_group
        text description
        string file_path
        string original_filename
        string mime_type
        timestamp deleted_at
        timestamps timestamps
    }

    child_worksheet_assignments {
        uuid id PK
        uuid child_profile_id FK,UK
        uuid worksheet_id FK,UK
        string status
        timestamp assigned_at
        timestamp started_at
        timestamp completed_at
        timestamps timestamps
    }

    games {
        uuid id PK
        string title
        string slug UK
        string age
        text description
        string category
        string route_name
        string thumbnail_path
        boolean is_visible
        timestamp deleted_at
        timestamps timestamps
    }

    child_game_permissions {
        uuid id PK
        uuid child_id FK,UK
        uuid game_id FK,UK
        boolean is_allowed
        timestamps timestamps
    }

    permissions {
        uuid id PK
        string name UK
        string label
        timestamps timestamps
    }

    permission_user {
        uuid permission_id PK,FK
        uuid user_id PK,FK
    }

    users ||--|| user_preferences : "has one"
    users ||--o{ categories : "owns"
    users ||--o{ tasks : "owns"
    users ||--o{ child_profiles : "parent"
    users ||--o{ learning_activities : "creates"
    users ||--o{ worksheets : "uploads"
    users ||--o{ permission_user : "assigned"
    permissions ||--o{ permission_user : "assigned"

    categories ||--o{ tasks : "groups"
    tasks ||--o{ tasks : "parent task"

    child_profiles ||--o{ child_progress : "tracks"
    learning_activities ||--o{ child_progress : "completed by child"

    child_profiles ||--o{ child_worksheet_assignments : "receives"
    worksheets ||--o{ child_worksheet_assignments : "assigned"

    child_profiles ||--o{ child_game_permissions : "game access"
    games ||--o{ child_game_permissions : "allowed for child"
```

## Relationship Summary

| Relationship | Laravel meaning |
| --- | --- |
| `User -> ChildProfile` | One parent user can have many child profiles. |
| `ChildProfile -> ChildProgress` | One child can have many progress records. |
| `LearningActivity -> ChildProgress` | One learning activity can appear in many child progress records. |
| `ChildProfile + LearningActivity -> ChildProgress` | The pair is unique, so one child has only one progress row per activity. |
| `User -> LearningActivity` | One parent user can create many learning activities. |
| `User -> Worksheet` | One user can upload many worksheets. |
| `ChildProfile + Worksheet -> ChildWorksheetAssignment` | The pair is unique, so one child receives one assignment row per worksheet. |
| `ChildProfile + Game -> ChildGamePermission` | The pair is unique, so one child has one permission row per game. |
| `User -> Task` | One user owns many tasks. |
| `Category -> Task` | One category can contain many tasks. A task category can be nullable. |
| `Task -> Task` | A task can have subtasks through `parent_task_id`. |
| `User -> UserPreference` | One user has one preference row because `user_id` is unique. |
| `User <-> Permission` | Many-to-many through `permission_user`. |

## Main Model Map

| Model | Table | Important relationships |
| --- | --- | --- |
| `User` | `users` | has many child profiles, tasks, learning activities, worksheets; belongs to many permissions |
| `ChildProfile` | `child_profiles` | belongs to parent user; has many progress rows, worksheet assignments, game permissions |
| `ChildProgress` | `child_progress` | belongs to child profile and learning activity |
| `LearningActivity` | `learning_activities` | belongs to parent user; has many child progress rows |
| `Worksheet` | `worksheets` | belongs to uploader user; has many child worksheet assignments |
| `ChildWorksheetAssignment` | `child_worksheet_assignments` | belongs to child profile and worksheet |
| `Game` | `games` | has many child game permissions |
| `ChildGamePermission` | `child_game_permissions` | belongs to child profile and game |
| `Task` | `tasks` | belongs to user, category, and optional parent task; has many subtasks |
| `Category` | `categories` | belongs to user; has many tasks |
| `Permission` | `permissions` | belongs to many users through `permission_user` |

## Laravel System Tables

These tables are also in the database but are not part of the main app domain ERD:

- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `personal_access_tokens`
