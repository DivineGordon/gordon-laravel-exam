# Architecture Documentation

## System Overview

The Page Customizer is a full-stack web application built with a modern architecture that separates concerns between the backend API and frontend client. The system follows RESTful principles and implements a clean separation of concerns.

## Architecture Diagram

```
┌─────────────────┐    HTTP/API     ┌─────────────────┐
│   Vue.js 3      │◄──────────────►│   Laravel 12    │
│   Frontend      │                 │   Backend       │
│                 │                 │                 │
│ ┌─────────────┐ │                 │ ┌─────────────┐ │
│ │   Router    │ │                 │ │ Controllers │ │
│ └─────────────┘ │                 │ └─────────────┘ │
│ ┌─────────────┐ │                 │ ┌─────────────┐ │
│ │   Pinia     │ │                 │ │   Models    │ │
│ │   Stores    │ │                 │ │             │ │
│ └─────────────┘ │                 │ └─────────────┘ │
│ ┌─────────────┐ │                 │ ┌─────────────┐ │
│ │ Components  │ │                 │ │  Database   │ │
│ └─────────────┘ │                 │ └─────────────┘ │
└─────────────────┘                 └─────────────────┘
```

## Backend Architecture (Laravel)

### MVC Pattern Implementation

The backend follows Laravel's MVC (Model-View-Controller) pattern:

#### Models (`app/Models/`)
- **User**: Handles user authentication and relationships
- **ClientPage**: Manages page content, themes, and publishing status
- **PageTheme**: Defines theme configurations and color schemes
- **PageAnalytic**: Tracks visitor data and analytics

#### Controllers (`app/Http/Controllers/`)
- **AuthController**: Handles user registration, login, and logout
- **ClientPageController**: Manages page CRUD operations and file uploads
- **PageThemeController**: Provides theme data
- **AnalyticsController**: Processes analytics queries and exports
- **PublicPageController**: Serves public pages to visitors

#### Key Features
- **Laravel Sanctum**: Token-based API authentication
- **Eloquent ORM**: Database abstraction and relationships
- **File Storage**: Handles logo and background image uploads
- **Validation**: Request validation for data integrity
- **CORS**: Cross-origin resource sharing configuration

### API Design

The API follows RESTful conventions:

```
Authentication:
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user

Page Management:
GET  /api/my-page
PUT  /api/my-page
POST /api/my-page/logo
POST /api/my-page/background

Themes:
GET  /api/themes

Analytics:
GET  /api/analytics
GET  /api/analytics/export

Public Access:
GET  /pages/{slug}
```

### Database Layer

- **SQLite**: Default database for development
- **Migrations**: Version-controlled database schema
- **Seeders**: Pre-populated theme data
- **Relationships**: Proper foreign key constraints and cascading deletes

## Frontend Architecture (Vue.js 3)

### Component-Based Architecture

The frontend uses Vue.js 3's Composition API with TypeScript for type safety:

#### Core Components
- **DashboardView**: Main application container with tab navigation
- **PageEditor**: Page customization interface with live preview
- **PagePreview**: Real-time preview of page changes
- **AnalyticsDashboard**: Analytics visualization with charts

#### State Management (Pinia)

```
stores/
├── auth.ts      # User authentication state
├── page.ts      # Page content and theme management
├── analytics.ts # Analytics data and chart state
└── counter.ts   # Example store (can be removed)
```

#### Routing (Vue Router)

```
Routes:
/               → Redirect to /dashboard
/login          → LoginView (guest only)
/register       → RegisterView (guest only)
/dashboard      → DashboardView (authenticated)
```

### Frontend Features

- **TypeScript**: Type safety and better development experience
- **Tailwind CSS**: Utility-first CSS framework for styling
- **Chart.js**: Interactive analytics charts
- **Axios**: HTTP client with interceptors
- **File Upload**: Drag-and-drop file handling
- **Responsive Design**: Mobile-first approach

## Data Flow

### Authentication Flow

```
1. User submits login form
2. Frontend sends credentials to /api/login
3. Laravel validates credentials
4. Sanctum generates API token
5. Token stored in localStorage
6. Token included in subsequent requests
```

### Page Customization Flow

```
1. User loads dashboard
2. Frontend fetches user's page data
3. User edits content in PageEditor
4. Changes reflected in PagePreview
5. User saves changes
6. Frontend sends PUT request to /api/my-page
7. Backend updates database
8. Success confirmation displayed
```

### Analytics Flow

```
1. Public page visited
2. PublicPageController records visit
3. Analytics data stored in database
4. Dashboard requests analytics
5. Backend aggregates data by period
6. Frontend displays charts and metrics
```

## Security Considerations

### Authentication & Authorization
- **Laravel Sanctum**: Secure token-based authentication
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Input Validation**: Server-side validation for all inputs
- **File Upload Security**: MIME type and size validation

### Data Protection
- **SQL Injection Prevention**: Eloquent ORM parameterized queries
- **XSS Protection**: Vue.js automatic escaping
- **CORS Configuration**: Controlled cross-origin access
- **Environment Variables**: Sensitive data in .env files

## Performance Optimizations

### Backend
- **Database Indexing**: Optimized queries with proper indexes
- **Eager Loading**: Prevents N+1 query problems
- **File Storage**: Efficient file handling with Laravel Storage
- **Caching**: Ready for Redis/Memcached integration

### Frontend
- **Code Splitting**: Vue Router lazy loading
- **Asset Optimization**: Vite build optimization
- **Chart Performance**: Efficient Chart.js rendering
- **State Management**: Minimal re-renders with Pinia

## Scalability Considerations

### Horizontal Scaling
- **Stateless API**: No server-side session dependency
- **Database Separation**: Can easily switch to PostgreSQL/MySQL
- **CDN Ready**: Static assets can be served from CDN
- **Load Balancer**: Multiple server instances supported

### Vertical Scaling
- **Database Optimization**: Query optimization and indexing
- **Caching Layer**: Redis integration ready
- **File Storage**: Can migrate to cloud storage (S3, etc.)
- **Queue System**: Background job processing ready

## Development Workflow

### Backend Development
1. Create migrations for database changes
2. Define models with relationships
3. Implement controllers with validation
4. Add API routes
5. Test with Postman/API client

### Frontend Development
1. Design components with TypeScript
2. Implement Pinia stores for state
3. Create Vue Router routes
4. Style with Tailwind CSS
5. Test in browser

### Integration Testing
1. Start Laravel development server
2. Start Vue.js development server
3. Test API endpoints
4. Verify frontend-backend communication
5. Test authentication flow

## Technology Decisions

### Why Laravel?
- **Rapid Development**: Built-in features and conventions
- **Security**: Built-in security features and best practices
- **Ecosystem**: Rich package ecosystem
- **Documentation**: Excellent documentation and community

### Why Vue.js 3?
- **Composition API**: Better code organization and reusability
- **TypeScript Support**: Excellent TypeScript integration
- **Performance**: Smaller bundle size and better performance
- **Developer Experience**: Great tooling and debugging

### Why SQLite?
- **Development**: Easy setup and no server required
- **Portability**: Single file database
- **Performance**: Fast for small to medium applications
- **Migration**: Easy to migrate to PostgreSQL/MySQL

## Future Enhancements

### Planned Features
- **Real-time Updates**: WebSocket integration for live collaboration
- **Advanced Analytics**: More detailed visitor tracking
- **Theme Editor**: Custom theme creation interface
- **Multi-language Support**: Internationalization
- **API Versioning**: Versioned API endpoints

### Technical Improvements
- **Testing**: Comprehensive test suite
- **CI/CD**: Automated deployment pipeline
- **Monitoring**: Application performance monitoring
- **Documentation**: API documentation with Swagger
- **Caching**: Redis integration for better performance
