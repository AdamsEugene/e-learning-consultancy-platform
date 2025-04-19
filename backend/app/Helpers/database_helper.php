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
        courses_count INTEGER DEFAULT 0,
        created_by INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );",
    "CREATE TABLE IF NOT EXISTS labels (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        name_slug VARCHAR(255) NOT NULL,
        description TEXT,
        color VARCHAR(255) DEFAULT '#000000',
        status VARCHAR(255) DEFAULT 'active',
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
        status VARCHAR(255) DEFAULT 'active',
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
        courses_count INTEGER DEFAULT 0,
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
        enrollmentCount INTEGER DEFAULT 0,
        image TEXT DEFAULT '',
        thumbnail TEXT DEFAULT '',
        tags TEXT DEFAULT '',
        level VARCHAR(255) DEFAULT '',
        category_id INTEGER DEFAULT 0,
        subcategory_id INTEGER DEFAULT 0,
        course_type VARCHAR(255) DEFAULT 'free',
        originalPrice INTEGER DEFAULT 0,
        price INTEGER DEFAULT 0,
        features TEXT DEFAULT '',
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
    CREATE INDEX IF NOT EXISTS idx_courses_reviews_course_id ON courses_reviews (course_id);
    CREATE INDEX IF NOT EXISTS idx_courses_reviews_user_id ON courses_reviews (user_id);",
    "CREATE TABLE IF NOT EXISTS courses_enrollments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        course_id INTEGER DEFAULT 0,
        user_id INTEGER DEFAULT 0,
        amount_payable INTEGER DEFAULT 0,
        amount_offered INTEGER DEFAULT 0,
        lessons_count INTEGER DEFAULT 0,
        lessons_completed INTEGER DEFAULT 0,
        current_lesson INTEGER DEFAULT 0,
        next_lesson INTEGER DEFAULT 0,
        status VARCHAR(255) DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_course_id ON courses_enrollments (course_id);
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_user_id ON courses_enrollments (user_id);
    CREATE INDEX IF NOT EXISTS idx_courses_enrollments_status ON courses_enrollments (status);",
    "CREATE TABLE IF NOT EXISTS wishlist (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER DEFAULT 0,
        course_id INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );"
];

$alterTables = [
    // "ALTER TABLE users ADD COLUMN reviewCount INTEGER DEFAULT 0;",
];

function createDatabaseStructure() {
    global $databases, $alterTables;
    $db = \Config\Database::connect();
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
