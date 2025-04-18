# Nuxt.js TypeScript E-Learning & Consultancy Platform Folder Structure

```
📁 e-learning-consultancy-platform/
├── 📁 assets/                       # Static assets that will be processed by the build tool
│   ├── 📁 fonts/                    # Custom fonts
│   ├── 📁 images/                   # Images used throughout the app
│   │   ├── 📁 icons/                # App icons
│   │   ├── 📁 illustrations/        # Illustrations for pages
│   │   └── 📁 logos/                # Logo variants
│   ├── 📁 styles/                   # Global styles
│   │   ├── 📄 main.scss             # Main stylesheet
│   │   ├── 📄 variables.scss        # SCSS variables
│   │   └── 📄 transitions.scss      # CSS transitions
│   └── 📁 videos/                   # Static video assets
│
├── 📁 components/                   # Vue components organized by feature
│   ├── 📁 common/                   # Shared components used across features
│   │   ├── 📁 buttons/              # Button components
│   │   ├── 📁 cards/                # Card components
│   │   ├── 📁 forms/                # Form components
│   │   ├── 📁 layout/               # Layout components
│   │   ├── 📁 loaders/              # Loading indicators
│   │   ├── 📁 modals/               # Modal components
│   │   └── 📁 navigation/           # Navigation components
│   │
│   ├── 📁 auth/                     # Authentication components
│   │   ├── 📄 LoginForm.vue
│   │   ├── 📄 RegisterForm.vue
│   │   ├── 📄 ForgotPasswordForm.vue
│   │   └── 📄 ResetPasswordForm.vue
│   │
│   ├── 📁 dashboard/                # Dashboard components
│   │   ├── 📁 student/              # Student dashboard components
│   │   ├── 📁 client/               # Client dashboard components
│   │   ├── 📁 consultant/           # Consultant dashboard components
│   │   └── 📁 admin/                # Admin dashboard components
│   │
│   ├── 📁 courses/                  # Course-related components
│   │   ├── 📄 CourseCard.vue
│   │   ├── 📄 CourseList.vue
│   │   ├── 📄 CourseSearch.vue
│   │   ├── 📄 CourseFilters.vue
│   │   ├── 📄 CourseSidebar.vue
│   │   ├── 📄 LessonPlayer.vue
│   │   ├── 📄 QuizComponent.vue
│   │   └── 📄 CertificateTemplate.vue
│   │
│   ├── 📁 consultancy/              # Consultancy-related components
│   │   ├── 📄 ProjectForm.vue
│   │   ├── 📄 ProjectCard.vue
│   │   ├── 📄 ProjectList.vue
│   │   ├── 📄 MilestoneTracker.vue
│   │   ├── 📄 BudgetProposal.vue
│   │   └── 📄 MeetingScheduler.vue
│   │
│   ├── 📁 messaging/                # Messaging components
│   │   ├── 📄 ChatWindow.vue
│   │   ├── 📄 MessageList.vue
│   │   ├── 📄 MessageInput.vue
│   │   └── 📄 ContactList.vue
│   │
│   └── 📁 video/                    # Video conferencing components
│       ├── 📄 VideoRoom.vue
│       ├── 📄 ParticipantsList.vue
│       ├── 📄 ControlPanel.vue
│       └── 📄 ScreenSharing.vue
│
├── 📁 composables/                  # Vue 3 composables
│   ├── 📄 useAuth.ts                # Authentication composable
│   ├── 📄 useCourses.ts             # Courses composable
│   ├── 📄 useProjects.ts            # Projects composable
│   ├── 📄 useUser.ts                # User data composable
│   ├── 📄 useNotifications.ts       # Notifications composable
│   └── 📄 useVideoConference.ts     # Video conferencing composable
│
├── 📁 layouts/                      # Nuxt layouts
│   ├── 📄 default.vue               # Default layout
│   ├── 📄 auth.vue                  # Authentication layout
│   ├── 📄 dashboard.vue             # Dashboard layout
│   ├── 📄 course.vue                # Course viewing layout
│   ├── 📄 admin.vue                 # Admin layout
│   └── 📄 error.vue                 # Error page layout
│
├── 📁 middleware/                   # Nuxt middleware
│   ├── 📄 auth.ts                   # Authentication middleware
│   ├── 📄 role.ts                   # Role-based access middleware
│   └── 📄 subscription.ts           # Subscription check middleware
│
├── 📁 pages/                        # Nuxt pages (routes)
│   ├── 📄 index.vue                 # Landing page
│   ├── 📁 auth/                     # Auth pages
│   │   ├── 📄 login.vue
│   │   ├── 📄 register.vue
│   │   ├── 📄 forgot-password.vue
│   │   └── 📄 reset-password.vue
│   │
│   ├── 📁 courses/                  # Course pages
│   │   ├── 📄 index.vue             # Course catalog
│   │   ├── 📄 [id].vue              # Course details
│   │   └── 📁 [id]/                 # Nested course routes
│   │       ├── 📄 lessons/[lessonId].vue
│   │       ├── 📄 quizzes/[quizId].vue
│   │       └── 📄 certificate.vue
│   │
│   ├── 📁 consultancy/              # Consultancy pages
│   │   ├── 📄 index.vue             # Consultancy services
│   │   ├── 📄 request.vue           # New project request
│   │   └── 📄 [id].vue              # Project details
│   │
│   ├── 📁 dashboard/                # Dashboard pages
│   │   ├── 📄 index.vue             # Main dashboard
│   │   ├── 📄 profile.vue           # User profile
│   │   ├── 📄 settings.vue          # User settings
│   │   ├── 📁 student/              # Student specific pages
│   │   ├── 📁 client/               # Client specific pages
│   │   ├── 📁 consultant/           # Consultant specific pages
│   │   └── 📁 admin/                # Admin specific pages
│   │
│   ├── 📁 messaging/                # Messaging pages
│   │   ├── 📄 index.vue             # Messaging dashboard
│   │   └── 📄 [id].vue              # Conversation
│   │
│   └── 📁 meetings/                 # Video conferencing pages
│       ├── 📄 index.vue             # Meetings list
│       ├── 📄 schedule.vue          # Schedule meeting
│       └── 📄 [id].vue              # Meeting room
│
├── 📁 plugins/                      # Nuxt plugins
│   ├── 📄 api.ts                    # API client
│   ├── 📄 auth.ts                   # Auth plugin
│   ├── 📄 vee-validate.ts           # Form validation
│   ├── 📄 video-conference.ts       # Video conferencing
│   └── 📄 notifications.ts          # Real-time notifications
│
├── 📁 public/                       # Static files served at root level
│   ├── 📄 favicon.ico
│   ├── 📄 robots.txt
│   ├── 📁 images/                   # Static images that don't need processing
│   └── 📁 videos/                   # Static video content
│
├── 📁 server/                       # Server-side code (if using SSR)
│   ├── 📁 api/                      # API routes
│   ├── 📁 middleware/               # Server middleware
│   └── 📁 plugins/                  # Server plugins
│
├── 📁 stores/                       # Pinia stores
│   ├── 📄 auth.ts                   # Authentication state
│   ├── 📄 user.ts                   # User state
│   ├── 📄 courses.ts                # Courses state
│   ├── 📄 projects.ts               # Projects state
│   ├── 📄 notifications.ts          # Notifications state
│   └── 📄 ui.ts                     # UI state (theme, sidebar, etc.)
│
├── 📁 types/                        # TypeScript type definitions
│   ├── 📄 user.ts                   # User-related types
│   ├── 📄 course.ts                 # Course-related types
│   ├── 📄 project.ts                # Project-related types
│   ├── 📄 notification.ts           # Notification types
│   └── 📄 api.ts                    # API response types
│
└── 📁 utils/                        # Utility functions
    ├── 📄 formatters.ts             # Data formatters
    ├── 📄 validators.ts             # Input validators
    ├── 📄 constants.ts              # Constants and enums
    ├── 📄 storage.ts                # Local storage helpers
    └── 📄 helpers.ts                # General helpers
```