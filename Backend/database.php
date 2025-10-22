<?php
require_once '../ClassAutoLoad.php'; // Include the autoloader

// Disable foreign key checks
$disable_fk_checks = $SQL->disableForeignKeyChecks();

// ============================================
// DROP TABLES (in reverse order of dependencies)
// ============================================
$drop_appointments = $SQL->dropTable('appointments');
$drop_availability = $SQL->dropTable('mentor_availability');
$drop_mentors = $SQL->dropTable('mentors');
$drop_patients = $SQL->dropTable('patients');
$drop_users = $SQL->dropTable('users');
$drop_roles = $SQL->dropTable('roles');
$drop_genders = $SQL->dropTable('genders');

// ============================================
// CREATE BASE TABLES
// ============================================

// Genders table
$create_genders = $SQL->createTable('genders', [
    'genderId' => 'tinyint(1) AUTO_INCREMENT PRIMARY KEY',
    'genderName' => 'VARCHAR(50) NOT NULL unique',
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

// Roles table
$create_roles = $SQL->createTable('roles', [
    'roleId' => 'tinyint(1) AUTO_INCREMENT PRIMARY KEY',
    'roleName' => 'VARCHAR(50) NOT NULL unique',
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

// Users table (base table for authentication)
$create_users = $SQL->createTable('users', [
    'userId' => 'bigint(10) AUTO_INCREMENT PRIMARY KEY',
    'fullname' => 'VARCHAR(100) NOT NULL',
    'email' => 'VARCHAR(100) NOT NULL unique',
    'phone' => 'VARCHAR(20) default NULL',
    'password' => 'VARCHAR(255) NOT NULL',
    'verify_code' => 'VARCHAR(10) default NULL',
    'code_expiry_time' => 'TIMESTAMP NULL DEFAULT NULL',
    'mustchange' => 'tinyint(1) not null default 0',
    'status' => "ENUM('Active', 'Inactive', 'Suspended', 'Pending', 'Deleted') DEFAULT 'Pending'",
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    'last_login' => 'TIMESTAMP NULL DEFAULT NULL',
    'roleId' => 'tinyint(1) not null default 1',
    'genderId' => 'tinyint(1) not null default 1'
]);

// ============================================
// PATIENTS TABLE
// ============================================
$create_patients = $SQL->createTable('patients', [
    'patientId' => 'bigint(10) AUTO_INCREMENT PRIMARY KEY',
    'userId' => 'bigint(10) NOT NULL unique',
    'date_of_birth' => 'DATE default NULL',
    'address' => 'TEXT default NULL',
    'emergency_contact' => 'VARCHAR(100) default NULL',
    'emergency_phone' => 'VARCHAR(20) default NULL',
    'medical_history' => 'TEXT default NULL',
    'current_medications' => 'TEXT default NULL',
    'preferred_session_type' => "ENUM('Online', 'Physical', 'Both') DEFAULT 'Both'",
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

// ============================================
// MENTORS TABLE
// ============================================
$create_mentors = $SQL->createTable('mentors', [
    'mentorId' => 'bigint(10) AUTO_INCREMENT PRIMARY KEY',
    'userId' => 'bigint(10) NOT NULL unique',
    'license_number' => 'VARCHAR(50) default NULL',
    'years_of_experience' => 'INT default 0',
    'education' => 'TEXT default NULL',
    'certifications' => 'TEXT default NULL',
    'session_types_offered' => "ENUM('Online', 'Physical', 'Both') DEFAULT 'Both'",
    'physical_location' => 'VARCHAR(255) default NULL',
    'consultation_fee' => 'DECIMAL(10,2) default 0.00',
    'rating' => 'DECIMAL(3,2) default 0.00',
    'total_reviews' => 'INT default 0',
    'is_verified' => 'tinyint(1) not null default 0',
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);


// ============================================
// MENTOR AVAILABILITY TABLE
// ============================================
$create_availability = $SQL->createTable('mentor_availability', [
    'availabilityId' => 'bigint(10) AUTO_INCREMENT PRIMARY KEY',
    'mentorId' => 'bigint(10) NOT NULL',
    'day_of_week' => "ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL",
    'start_time' => 'TIME NOT NULL',
    'end_time' => 'TIME NOT NULL',
    'is_active' => 'tinyint(1) not null default 1',
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

// ============================================
// APPOINTMENTS TABLE
// ============================================
$create_appointments = $SQL->createTable('appointments', [
    'appointmentId' => 'bigint(10) AUTO_INCREMENT PRIMARY KEY',
    'patientId' => 'bigint(10) NOT NULL',
    'mentorId' => 'bigint(10) NOT NULL',
    'appointment_date' => 'DATE NOT NULL',
    'appointment_time' => 'TIME NOT NULL',
    'session_type' => "ENUM('Online', 'Physical') NOT NULL",
    'meeting_link' => 'VARCHAR(255) default NULL',
    'physical_location' => 'VARCHAR(255) default NULL',
    'duration_minutes' => 'INT default 60',
    'status' => "ENUM('Scheduled', 'Confirmed', 'Completed', 'Cancelled', 'No-Show') DEFAULT 'Scheduled'",
    'notes' => 'TEXT default NULL',
    'cancellation_reason' => 'TEXT default NULL',
    'created' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
    'updated' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
]);

// ============================================
// ADD FOREIGN KEY CONSTRAINTS
// ============================================

// Users table constraints
$alter_users_roles = $SQL->addConstraint('users', 'roles', 'roleId', 'CASCADE', 'CASCADE');
$alter_users_genders = $SQL->addConstraint('users', 'genders', 'genderId', 'CASCADE', 'CASCADE');

// Patients table constraints
$alter_patients_users = $SQL->addConstraint('patients', 'users', 'userId', 'CASCADE', 'CASCADE');

// Mentors table constraints
$alter_mentors_users = $SQL->addConstraint('mentors', 'users', 'userId', 'CASCADE', 'CASCADE');

// Mentor specializations constraints
$alter_mentor_spec_mentors = $SQL->addConstraint('mentor_specializations', 'mentors', 'mentorId', 'CASCADE', 'CASCADE');
$alter_mentor_spec_specializations = $SQL->addConstraint('mentor_specializations', 'specializations', 'specializationId', 'CASCADE', 'CASCADE');

// Mentor availability constraints
$alter_availability_mentors = $SQL->addConstraint('mentor_availability', 'mentors', 'mentorId', 'CASCADE', 'CASCADE');

// Appointments constraints
$alter_appointments_patients = $SQL->addConstraint('appointments', 'patients', 'patientId', 'CASCADE', 'CASCADE');
$alter_appointments_mentors = $SQL->addConstraint('appointments', 'mentors', 'mentorId', 'CASCADE', 'CASCADE');

// Enable foreign key checks
$enable_fk_checks = $SQL->enableForeignKeyChecks();

// ============================================
// DISPLAY OPERATION RESULTS
// ============================================
$operations = [
    'Disable Foreign Key Checks' => $disable_fk_checks,
    'Drop Appointments Table' => $drop_appointments,
    'Drop Mentor Availability Table' => $drop_availability,
    'Drop Mentor Specializations Table' => $drop_mentor_specializations,
    'Drop Specializations Table' => $drop_specializations,
    'Drop Mentors Table' => $drop_mentors,
    'Drop Patients Table' => $drop_patients,
    'Drop Users Table' => $drop_users,
    'Drop Roles Table' => $drop_roles,
    'Drop Genders Table' => $drop_genders,
    'Create Genders Table' => $create_genders,
    'Create Roles Table' => $create_roles,
    'Create Users Table' => $create_users,
    'Create Patients Table' => $create_patients,
    'Create Mentors Table' => $create_mentors,
    'Create Specializations Table' => $create_specializations,
    'Create Mentor Specializations Table' => $create_mentor_specializations,
    'Create Mentor Availability Table' => $create_availability,
    'Create Appointments Table' => $create_appointments,
    'Add Users-Roles Constraint' => $alter_users_roles,
    'Add Users-Genders Constraint' => $alter_users_genders,
    'Add Patients-Users Constraint' => $alter_patients_users,
    'Add Mentors-Users Constraint' => $alter_mentors_users,
    'Add Mentor Specializations-Mentors Constraint' => $alter_mentor_spec_mentors,
    'Add Mentor Specializations-Specializations Constraint' => $alter_mentor_spec_specializations,
    'Add Availability-Mentors Constraint' => $alter_availability_mentors,
    'Add Appointments-Patients Constraint' => $alter_appointments_patients,
    'Add Appointments-Mentors Constraint' => $alter_appointments_mentors,
    'Enable Foreign Key Checks' => $enable_fk_checks
];

foreach ($operations as $operation => $result) {
    if ($result) {
        echo "$operation: Success | " . date('Y-m-d H:i:s') . "\n";
    } else {
        echo "$operation: Failed | " . date('Y-m-d H:i:s') . "\n";
    }
}

echo "\n==============================================\n";
echo "Database schema created successfully!\n";
echo "==============================================\n";
?>