# Database Schema Documentation

## Overview

The Page Customizer application uses a relational database design with SQLite as the default database engine. The schema is designed to support user management, page customization, theme management, and analytics tracking.

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│      users      │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ role            │
│ created_at      │
│ updated_at      │
└─────────────────┘
         │
         │ 1:1
         │
┌─────────────────┐
│  client_pages   │
├─────────────────┤
│ id (PK)         │
│ user_id (FK)    │◄──┐
│ slug            │   │
│ content (JSON)  │   │
│ logo_path       │   │
│ bg_image_path   │   │
│ theme_id (FK)   │───┼──┐
│ is_published    │   │  │
│ created_at      │   │  │
│ updated_at      │   │  │
└─────────────────┘   │  │
         │            │  │
         │ 1:N        │  │
         │            │  │
┌─────────────────┐   │  │
│ page_analytics  │   │  │
├─────────────────┤   │  │
│ id (PK)         │   │  │
│ client_page_id  │───┘  │
│ visitor_ip      │      │
│ session_id      │      │
│ user_agent      │      │
│ referer         │      │
│ visited_at      │      │
│ created_at      │      │
│ updated_at      │      │
└─────────────────┘      │
                         │
┌─────────────────┐      │
│  page_themes    │      │
├─────────────────┤      │
│ id (PK)         │◄─────┘
│ name            │
│ primary_color   │
│ secondary_color │
│ accent_color    │
│ text_color      │
│ background_color│
│ created_at      │
│ updated_at      │
└─────────────────┘
```

## Table Descriptions

### users

Stores user account information and authentication data.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| name | VARCHAR(255) | NOT NULL | User's display name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User's email address |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| role | VARCHAR(255) | DEFAULT 'user' | User role (user/admin) |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| remember_token | VARCHAR(100) | NULLABLE | Remember me token |
| created_at | TIMESTAMP | NOT NULL | Record creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Record update timestamp |

**Indexes:**
- Primary key on `id`
- Unique index on `email`

**Relationships:**
- One-to-one with `client_pages` (hasOne)

### client_pages

Stores customizable page content and configuration for each user.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique page identifier |
| user_id | BIGINT | FOREIGN KEY, NOT NULL | Reference to users.id |
| slug | VARCHAR(255) | UNIQUE, NOT NULL | URL-friendly page identifier |
| content | JSON | NOT NULL | Page content structure |
| logo_path | VARCHAR(255) | NULLABLE | Path to uploaded logo |
| background_image_path | VARCHAR(255) | NULLABLE | Path to background image |
| theme_id | BIGINT | FOREIGN KEY, NULLABLE | Reference to page_themes.id |
| is_published | BOOLEAN | DEFAULT FALSE | Publication status |
| created_at | TIMESTAMP | NOT NULL | Record creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Record update timestamp |

**Indexes:**
- Primary key on `id`
- Unique index on `slug`
- Foreign key index on `user_id`
- Foreign key index on `theme_id`

**Foreign Key Constraints:**
- `user_id` → `users.id` (CASCADE DELETE)
- `theme_id` → `page_themes.id` (SET NULL)

**Relationships:**
- Belongs to `users` (belongsTo)
- Belongs to `page_themes` (belongsTo)
- One-to-many with `page_analytics` (hasMany)

**Content JSON Structure:**
```json
{
  "hero_title": "Welcome to My Page",
  "hero_subtitle": "This is a subtitle",
  "about_title": "About Us",
  "about_text": "About section content",
  "contact_title": "Contact",
  "contact_text": "Contact information"
}
```

### page_themes

Stores predefined theme configurations with color schemes.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique theme identifier |
| name | VARCHAR(255) | NOT NULL | Theme display name |
| primary_color | VARCHAR(7) | NOT NULL | Primary color (hex) |
| secondary_color | VARCHAR(7) | NOT NULL | Secondary color (hex) |
| accent_color | VARCHAR(7) | NOT NULL | Accent color (hex) |
| text_color | VARCHAR(7) | NOT NULL | Text color (hex) |
| background_color | VARCHAR(7) | NOT NULL | Background color (hex) |
| created_at | TIMESTAMP | NOT NULL | Record creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Record update timestamp |

**Indexes:**
- Primary key on `id`

**Relationships:**
- One-to-many with `client_pages` (hasMany)

**Sample Theme Data:**
```json
{
  "name": "Ocean Blue",
  "primary_color": "#1e40af",
  "secondary_color": "#3b82f6",
  "accent_color": "#06b6d4",
  "text_color": "#1f2937",
  "background_color": "#f8fafc"
}
```

### page_analytics

Tracks visitor analytics and page view statistics.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique analytics record ID |
| client_page_id | BIGINT | FOREIGN KEY, NOT NULL | Reference to client_pages.id |
| visitor_ip | VARCHAR(45) | NOT NULL | Visitor's IP address (IPv4/IPv6) |
| session_id | VARCHAR(255) | NOT NULL | Unique session identifier |
| user_agent | TEXT | NOT NULL | Browser user agent string |
| referer | VARCHAR(255) | NULLABLE | HTTP referer URL |
| visited_at | TIMESTAMP | NOT NULL | Visit timestamp |
| created_at | TIMESTAMP | NOT NULL | Record creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Record update timestamp |

**Indexes:**
- Primary key on `id`
- Foreign key index on `client_page_id`
- Composite index on `client_page_id, visited_at`
- Index on `session_id`

**Foreign Key Constraints:**
- `client_page_id` → `client_pages.id` (CASCADE DELETE)

**Relationships:**
- Belongs to `client_pages` (belongsTo)

## Database Migrations

### Migration Files

1. **0001_01_01_000000_create_users_table.php**
   - Creates the users table with authentication fields
   - Includes email verification and remember token fields

2. **2025_10_27_110042_create_client_pages_table.php**
   - Creates the client_pages table
   - Sets up foreign key relationships
   - Defines JSON content field

3. **2025_10_27_110104_create_page_themes_table.php**
   - Creates the page_themes table
   - Defines color scheme fields

4. **2025_10_27_110128_create_page_analytics_table.php**
   - Creates the page_analytics table
   - Sets up performance indexes
   - Defines visitor tracking fields

5. **2025_10_27_110218_add_role_to_users_table.php**
   - Adds role field to users table
   - Enables admin/user role distinction

## Database Seeders

### PageThemeSeeder

Pre-populates the database with default themes:

```php
$themes = [
    [
        'name' => 'Ocean Blue',
        'primary_color' => '#1e40af',
        'secondary_color' => '#3b82f6',
        'accent_color' => '#06b6d4',
        'text_color' => '#1f2937',
        'background_color' => '#f8fafc',
    ],
    [
        'name' => 'Forest Green',
        'primary_color' => '#059669',
        'secondary_color' => '#10b981',
        'accent_color' => '#34d399',
        'text_color' => '#064e3b',
        'background_color' => '#f0fdf4',
    ],
    // Additional themes...
];
```

## Query Patterns

### Common Queries

#### User Page Retrieval
```sql
SELECT cp.*, pt.* 
FROM client_pages cp
LEFT JOIN page_themes pt ON cp.theme_id = pt.id
WHERE cp.user_id = ?
```

#### Analytics Aggregation
```sql
SELECT 
    DATE(visited_at) as date,
    COUNT(*) as views,
    COUNT(DISTINCT session_id) as unique_visitors
FROM page_analytics 
WHERE client_page_id = ? 
    AND visited_at >= ?
GROUP BY DATE(visited_at)
ORDER BY date
```

#### Theme Usage Statistics
```sql
SELECT 
    pt.name,
    COUNT(cp.id) as usage_count
FROM page_themes pt
LEFT JOIN client_pages cp ON pt.id = cp.theme_id
GROUP BY pt.id, pt.name
ORDER BY usage_count DESC
```

## Performance Considerations

### Indexing Strategy

1. **Primary Keys**: All tables have auto-incrementing primary keys
2. **Foreign Keys**: All foreign key columns are indexed
3. **Unique Constraints**: Email and slug fields have unique indexes
4. **Composite Indexes**: Analytics table has composite index for efficient date range queries
5. **Session Tracking**: Session ID index for visitor uniqueness queries

### Query Optimization

1. **Eager Loading**: Use Eloquent's `with()` method to prevent N+1 queries
2. **Date Range Queries**: Analytics queries use indexed date columns
3. **Pagination**: Large result sets should be paginated
4. **Caching**: Consider caching frequently accessed theme data

## Data Integrity

### Constraints

1. **Foreign Key Constraints**: Ensure referential integrity
2. **Cascade Deletes**: User deletion removes associated pages and analytics
3. **Unique Constraints**: Prevent duplicate emails and slugs
4. **NOT NULL Constraints**: Ensure required fields are populated

### Validation Rules

1. **Email Format**: Valid email address format
2. **Color Format**: Hex color codes (#RRGGBB)
3. **Slug Format**: URL-friendly alphanumeric strings
4. **JSON Structure**: Valid JSON for content field

## Backup and Maintenance

### Backup Strategy

1. **SQLite Backup**: Copy the database.sqlite file
2. **Export Scripts**: Create data export utilities
3. **Migration Backup**: Keep migration files in version control

### Maintenance Tasks

1. **Analytics Cleanup**: Archive old analytics data
2. **File Cleanup**: Remove orphaned uploaded files
3. **Index Maintenance**: Monitor query performance
4. **Data Validation**: Regular integrity checks

## Migration to Production Database

### PostgreSQL Migration

To migrate from SQLite to PostgreSQL:

1. Update `.env` configuration:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=page_customizer
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Run migrations:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### MySQL Migration

Similar process for MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=page_customizer
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Security Considerations

### Data Protection

1. **Password Hashing**: Laravel's bcrypt hashing
2. **SQL Injection**: Eloquent ORM prevents SQL injection
3. **Input Validation**: Server-side validation for all inputs
4. **File Upload Security**: MIME type and size validation

### Privacy Compliance

1. **IP Address Storage**: Consider anonymization for GDPR compliance
2. **Data Retention**: Implement data retention policies
3. **User Consent**: Track user consent for analytics
4. **Data Export**: Provide user data export functionality
