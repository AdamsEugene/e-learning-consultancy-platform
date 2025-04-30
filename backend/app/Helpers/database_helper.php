<?php
global $databases, $alterTables;

use CodeIgniter\Database\Exceptions\DatabaseException;

// Create the databases
$databases = [
    "CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        name_slug VARCHAR(255) NOT NULL,
        description TEXT,
        image TEXT,
        parent_id INTEGER DEFAULT 0,
        preferred_order INTEGER DEFAULT 0,
        coursesCount INTEGER DEFAULT 0,
        created_by INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'Active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS analytics (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        recordType VARCHAR(255) NOT NULL,
        totalCount INTEGER DEFAULT 0,
        recordContent TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS labels (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        name_slug VARCHAR(255) NOT NULL,
        description TEXT,
        color VARCHAR(255) DEFAULT '#000000',
        status VARCHAR(255) DEFAULT 'Active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        firstname VARCHAR(255) NOT NULL,
        lastname VARCHAR(255) NOT NULL,
        image TEXT DEFAULT '',
        status VARCHAR(255) DEFAULT 'Active',
        description TEXT DEFAULT '',
        two_factor_setup VARCHAR(255) DEFAULT 'no',
        twofactor_secret VARCHAR(255) DEFAULT '',
        user_type VARCHAR(255) DEFAULT 'Student',
        admin_access VARCHAR(255) DEFAULT 'no',
        date_registered DATETIME DEFAULT CURRENT_TIMESTAMP,
        nationality VARCHAR(255) DEFAULT '',
        gender VARCHAR(255) DEFAULT '',
        timezone VARCHAR(255) DEFAULT '',
        website VARCHAR(255) DEFAULT '',
        company VARCHAR(255) DEFAULT '',
        language VARCHAR(255) DEFAULT '',
        preferences TEXT DEFAULT '',
        job_title VARCHAR(255) DEFAULT '',
        skills TEXT DEFAULT '',
        rating INTEGER DEFAULT 0,
        reviewCount INTEGER DEFAULT 0,
        students_count INTEGER DEFAULT 0,
        coursesCount INTEGER DEFAULT 0,
        last_login DATETIME DEFAULT NULL,
        date_of_birth DATETIME DEFAULT '',
        phone VARCHAR(255) DEFAULT '',
        billing_address TEXT DEFAULT '',
        permissions TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        social_links TEXT DEFAULT ''
    );
    CREATE INDEX IF NOT EXISTS idx_users_username ON users (username);
    CREATE INDEX IF NOT EXISTS idx_users_email ON users (email);
    CREATE INDEX IF NOT EXISTS idx_users_status ON users (status);
    CREATE INDEX IF NOT EXISTS idx_users_user_type ON users (user_type);",
    "CREATE TABLE IF NOT EXISTS user_token_auth (
        idusertokenauth INTEGER PRIMARY KEY AUTOINCREMENT,
        login TEXT,
        description TEXT,
        password TEXT UNIQUE,
        hash_algo TEXT,
        system_token INTEGER NOT NULL DEFAULT 0,
        last_used DATETIME DEFAULT NULL,
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
        date_expired DATETIME DEFAULT CURRENT_TIMESTAMP,
        ipaddress TEXT DEFAULT NULL
    );
    CREATE INDEX IF NOT EXISTS idx_user_token_auth_login ON user_token_auth (login);",
    "CREATE TABLE IF NOT EXISTS temp_table (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id TEXT,
        ver_code TEXT,
        username TEXT,
        full_name TEXT,
        email TEXT,
        pass TEXT,
        auth TEXT,
        time_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        ipaddress TEXT,
        request TEXT DEFAULT 'register',
        is_test INTEGER NOT NULL DEFAULT 0
    );
    CREATE INDEX IF NOT EXISTS idx_temp_table_user_id ON temp_table (user_id);
    CREATE INDEX IF NOT EXISTS idx_temp_table_username ON temp_table (username);
    CREATE INDEX IF NOT EXISTS idx_temp_table_email ON temp_table (email);",
    "CREATE TABLE IF NOT EXISTS courses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255) DEFAULT '',
        title_slug VARCHAR(255) NOT NULL,
        rating INTEGER DEFAULT 0,
        reviewCount INTEGER DEFAULT 0,
        viewsCount INTEGER DEFAULT 0,
        enrollmentCount INTEGER DEFAULT 0,
        image TEXT DEFAULT '',
        thumbnail TEXT DEFAULT '',
        tags TEXT DEFAULT '',
        is_featured TEXT DEFAULT 'no',
        level VARCHAR(255) DEFAULT '',
        category_id INTEGER DEFAULT 0,
        subcategory_id INTEGER DEFAULT 0,
        course_type VARCHAR(255) DEFAULT 'free',
        originalPrice INTEGER DEFAULT 0,
        price INTEGER DEFAULT 0,
        features TEXT DEFAULT '',
        language VARCHAR(255) DEFAULT 'English',
        visibility VARCHAR(255) DEFAULT 'Public',
        allow_discussion VARCHAR(255) DEFAULT 'yes',
        certification VARCHAR(255) DEFAULT 'no',
        description TEXT DEFAULT '',
        course_duration INTEGER DEFAULT 0,
        what_you_will_learn TEXT DEFAULT '',
        requirements TEXT DEFAULT '',
        created_by INTEGER DEFAULT 0,
        status VARCHAR(20) DEFAULT 'Unpublished',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_title_slug ON courses (title_slug);
    CREATE INDEX IF NOT EXISTS idx_courses_status ON courses (status);
    CREATE INDEX IF NOT EXISTS idx_courses_created_by ON courses (created_by);
    CREATE INDEX IF NOT EXISTS idx_courses_category_id ON courses (category_id);
    CREATE INDEX IF NOT EXISTS idx_courses_course_type ON courses (course_type);",
    "CREATE TABLE IF NOT EXISTS courses_instructors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        instructor_id INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_instructors_course_id ON courses_instructors (course_id);
    CREATE INDEX IF NOT EXISTS idx_courses_instructors_instructor_id ON courses_instructors (instructor_id);",
    "CREATE TABLE IF NOT EXISTS courses_tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        tag_id INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_tags_course_id ON courses_tags (course_id);
    CREATE INDEX IF NOT EXISTS idx_courses_tags_tag_id ON courses_tags (tag_id);",
    "CREATE TABLE IF NOT EXISTS courses_content (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        title VARCHAR(255) DEFAULT '',
        lessons TEXT DEFAULT '',
        totalDuration INTEGER DEFAULT 0,
        totalLessons INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_content_course_id ON courses_content (course_id);",
    "CREATE TABLE IF NOT EXISTS courses_reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        record_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        rating INTEGER DEFAULT 0,
        content TEXT DEFAULT '',
        entityType VARCHAR(255) DEFAULT 'Course',
        helpfulCount INTEGER DEFAULT 0,
        dislikesCount INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_reviews_record_id ON courses_reviews (record_id);
    CREATE INDEX IF NOT EXISTS idx_courses_reviews_user_id ON courses_reviews (user_id);",
    "CREATE TABLE IF NOT EXISTS courses_enrollments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        amountPayable INTEGER DEFAULT 0,
        amountOffered INTEGER DEFAULT 0,
        lessonsCount INTEGER DEFAULT 0,
        sectionsCount INTEGER DEFAULT 0,
        lessonsCompleted INTEGER DEFAULT 0,
        currentLesson INTEGER DEFAULT 0,
        nextLesson INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_course_id ON courses_enrollments (course_id);
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_user_id ON courses_enrollments (user_id);
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_status ON courses_enrollments (status);",
    "CREATE TABLE IF NOT EXISTS lesson_discussions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        lesson_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        votes INTEGER DEFAULT 0,
        parent_id INTEGER DEFAULT 0,
        content TEXT DEFAULT '',
        discussion_hash TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_lesson_discussions_course_id ON lesson_discussions (course_id);
    CREATE INDEX IF NOT EXISTS idx_lesson_discussions_lesson_id ON lesson_discussions (lesson_id);
    CREATE INDEX IF NOT EXISTS idx_lesson_discussions_user_id ON lesson_discussions (user_id);",
    "CREATE TABLE IF NOT EXISTS lesson_notes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        lesson_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        content TEXT DEFAULT '',
        note_hash TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_lesson_notes_course_id ON lesson_notes (course_id);
    CREATE INDEX IF NOT EXISTS idx_lesson_notes_lesson_id ON lesson_notes (lesson_id);
    CREATE INDEX IF NOT EXISTS idx_lesson_notes_user_id ON lesson_notes (user_id);",
    "CREATE TABLE IF NOT EXISTS wishlist (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        course_id INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_wishlist_course_id ON wishlist (course_id);
    CREATE INDEX IF NOT EXISTS idx_wishlist_user_id ON wishlist (user_id);",
    "CREATE TABLE IF NOT EXISTS classes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) DEFAULT '',
        description TEXT DEFAULT '',
        course_id INTEGER DEFAULT 0,
        class_type VARCHAR(255) DEFAULT 'Live',
        class_date DATETIME DEFAULT '',
        class_time DATETIME DEFAULT '',
        class_duration INTEGER DEFAULT 0,
        class_link TEXT DEFAULT '',
        class_password TEXT DEFAULT '',
        materials TEXT DEFAULT '',
        students_list TEXT DEFAULT '',
        user_id INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_classes_course_id ON classes (course_id);
    CREATE INDEX IF NOT EXISTS idx_classes_class_type ON classes (class_type);
    CREATE INDEX IF NOT EXISTS idx_classes_status ON classes (status);
    CREATE INDEX IF NOT EXISTS idx_classes_user_id ON classes (user_id);",
    "CREATE TABLE IF NOT EXISTS class_attendees (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        class_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_class_attendees_class_id ON class_attendees (class_id);
    CREATE INDEX IF NOT EXISTS idx_class_attendees_user_id ON class_attendees (user_id);",
    "CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        title TEXT DEFAULT '',
        description TEXT DEFAULT '',
        link TEXT DEFAULT '',
        read TEXT DEFAULT 'no',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications (user_id);",
    "CREATE TABLE IF NOT EXISTS activities (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        activity_type TEXT DEFAULT '',
        section TEXT DEFAULT '',
        content TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_activities_user_id ON activities (user_id);
    CREATE INDEX IF NOT EXISTS idx_activities_activity_type ON activities (activity_type);
    CREATE INDEX IF NOT EXISTS idx_activities_section ON activities (section);",
    "CREATE TABLE IF NOT EXISTS assignments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        course_id INTEGER DEFAULT 0,
        lesson_id INTEGER DEFAULT 0,
        assignment_hash TEXT DEFAULT '',
        assignment_type TEXT DEFAULT '',
        instructions TEXT DEFAULT '',
        content TEXT DEFAULT '',
        status TEXT DEFAULT '',
        submissionCount INTEGER DEFAULT 0,
        studentsList TEXT DEFAULT '',
        studentsCount INTEGER DEFAULT 0,
        allowAttachments TEXT DEFAULT 'no',
        maximumFileSize INTEGER DEFAULT 0,
        allowedFileTypes TEXT DEFAULT '',
        dueDate DATETIME DEFAULT '',
        lateSubmissionDate DATETIME DEFAULT '',
        gradingCriteria TEXT DEFAULT '',
        sendNotifications TEXT DEFAULT 'no',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_assignments_user_id ON assignments (user_id);
    CREATE INDEX IF NOT EXISTS idx_assignments_course_id ON assignments (course_id);
    CREATE INDEX IF NOT EXISTS idx_assignments_lesson_id ON assignments (lesson_id);
    CREATE INDEX IF NOT EXISTS idx_assignments_assignment_hash ON assignments (assignment_hash);
    CREATE INDEX IF NOT EXISTS idx_assignments_assignment_type ON assignments (assignment_type);
    CREATE INDEX IF NOT EXISTS idx_assignments_status ON assignments (status);",
    "CREATE TABLE IF NOT EXISTS assignment_submissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        assignment_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        content TEXT DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_assignment_submissions_assignment_id ON assignment_submissions (assignment_id);
    CREATE INDEX IF NOT EXISTS idx_assignment_submissions_user_id ON assignment_submissions (user_id);",
    "CREATE TABLE IF NOT EXISTS resources (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        record_id INTEGER DEFAULT 0,
        record_type TEXT DEFAULT '',
        user_id INTEGER DEFAULT 0,
        file_name TEXT DEFAULT '',
        file_path TEXT DEFAULT '',
        file_type TEXT DEFAULT '',
        file_size INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_resources_record_id ON resources (record_id);
    CREATE INDEX IF NOT EXISTS idx_resources_record_type ON resources (record_type);
    CREATE INDEX IF NOT EXISTS idx_resources_user_id ON resources (user_id);",
];

$alterTables = [
    "ALTER TABLE courses ADD COLUMN language TEXT DEFAULT 'English';",
    "ALTER TABLE courses ADD COLUMN visibility TEXT DEFAULT 'Public';",
    "ALTER TABLE courses ADD COLUMN allow_discussion TEXT DEFAULT 'yes';",
    "ALTER TABLE courses ADD COLUMN certification TEXT DEFAULT 'no';"
];

function createDatabaseStructure() {
    global $databases, $alterTables;
    $db = \Config\Database::connect();
    // $db->query("drop table if exists lesson_notes");
    foreach(array_merge($databases, $alterTables) as $query) {
        try {
            if(empty($query)) continue;
            $db->query($query);
        } catch(DatabaseException $e) {
        }
    }
}

/**
 * Set the database settings
 * 
 * @param object $dbHandler
 * 
 * @return void
 */
function setDatabaseSettings($dbHandler) {
    $dbHandler->query("PRAGMA journal_mode = WAL");
    $dbHandler->query("PRAGMA synchronous = NORMAL");
    $dbHandler->query("PRAGMA locking_mode = NORMAL");
    $dbHandler->query("PRAGMA busy_timeout = 5000");
    $dbHandler->query("PRAGMA cache_size = -16000");
}
