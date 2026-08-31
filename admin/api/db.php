<?php
// admin/api/db.php

$db_file = __DIR__ . '/rabss_os.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Auto-create database tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'Super Admin',
            avatar TEXT,
            status TEXT DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS inquiries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            company TEXT,
            country TEXT,
            project_type TEXT,
            budget TEXT,
            timeline TEXT,
            description TEXT,
            whatsapp TEXT,
            source TEXT DEFAULT 'Website Form',
            status TEXT DEFAULT 'New',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            company TEXT,
            country TEXT,
            email TEXT,
            phone TEXT,
            service TEXT,
            estimated_value REAL DEFAULT 0,
            currency TEXT DEFAULT 'USD',
            stage TEXT DEFAULT 'New Inquiry',
            source TEXT DEFAULT 'Outreach',
            assigned_to TEXT,
            last_contact DATETIME,
            next_followup DATETIME,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS clients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            company TEXT,
            country TEXT,
            email TEXT NOT NULL,
            phone TEXT,
            currency TEXT DEFAULT 'USD',
            status TEXT DEFAULT 'Active',
            total_revenue REAL DEFAULT 0,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            client_id INTEGER,
            name TEXT NOT NULL,
            project_type TEXT,
            budget REAL DEFAULT 0,
            currency TEXT DEFAULT 'USD',
            start_date DATE,
            deadline DATE,
            status TEXT DEFAULT 'Planning',
            progress INTEGER DEFAULT 0,
            priority TEXT DEFAULT 'Medium',
            assigned_team TEXT,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            project_id INTEGER,
            title TEXT NOT NULL,
            description TEXT,
            assignee TEXT,
            priority TEXT DEFAULT 'Medium',
            due_date DATE,
            status TEXT DEFAULT 'To Do',
            estimated_hours REAL DEFAULT 0,
            actual_hours REAL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_number TEXT UNIQUE NOT NULL,
            client_id INTEGER,
            project_id INTEGER,
            issue_date DATE,
            due_date DATE,
            currency TEXT DEFAULT 'USD',
            subtotal REAL DEFAULT 0,
            tax REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            total REAL DEFAULT 0,
            status TEXT DEFAULT 'Sent',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS invoice_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_id INTEGER,
            description TEXT NOT NULL,
            quantity REAL DEFAULT 1,
            unit_price REAL DEFAULT 0,
            total REAL DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS payments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_id INTEGER,
            client_id INTEGER,
            amount REAL NOT NULL,
            currency TEXT DEFAULT 'USD',
            payment_method TEXT DEFAULT 'Bank Transfer',
            transaction_ref TEXT,
            payment_date DATE,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            category TEXT NOT NULL,
            amount REAL NOT NULL,
            currency TEXT DEFAULT 'USD',
            vendor TEXT,
            project_id INTEGER,
            expense_date DATE,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS automations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            trigger_event TEXT NOT NULL,
            condition_field TEXT,
            condition_value TEXT,
            action_type TEXT NOT NULL,
            action_payload TEXT,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_name TEXT,
            action TEXT NOT NULL,
            entity TEXT NOT NULL,
            entity_id TEXT,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS meetings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            room_id TEXT UNIQUE NOT NULL,
            title TEXT NOT NULL,
            invitee_name TEXT NOT NULL,
            invitee_email TEXT NOT NULL,
            scheduled_at DATETIME NOT NULL,
            status TEXT DEFAULT 'Scheduled',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Seed default Super Admin if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $passHash = password_hash('Admin@RABSS2026', PASSWORD_DEFAULT);
        $pdo->exec("
            INSERT INTO users (name, email, password, role) 
            VALUES ('Subash Sitaula', 'ceo@rabss.tech', '{$passHash}', 'Super Admin');
        ");
    }

    // Seed default automations if empty
    $stmt_auto = $pdo->query("SELECT COUNT(*) FROM automations");
    if ($stmt_auto->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO automations (name, trigger_event, action_type, action_payload, is_active)
            VALUES ('Auto-Promote Inquiry to CRM Lead', 'inquiry_created', 'create_lead', '{\"assigned_to\": \"Subash Sitaula\"}', 1);

            INSERT INTO automations (name, trigger_event, action_type, action_payload, is_active)
            VALUES ('Auto Transactional Welcome Email', 'inquiry_created', 'send_email', '{\"subject\": \"Inquiry Received — RABSS Technologies\"}', 1);
        ");
    }

    // Ensure stage change notification automation exists
    try {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM automations WHERE trigger_event = ?");
        $stmt_check->execute(['lead_stage_changed']);
        if ($stmt_check->fetchColumn() == 0) {
            $pdo->exec("
                INSERT INTO automations (name, trigger_event, action_type, action_payload, is_active)
                VALUES ('Lead Stage Changed — Dynamic Stage Notification', 'lead_stage_changed', 'send_email', '{}', 1);
            ");
        }
    } catch (PDOException $migration_err) {
        error_log("Failed to seed lead stage automation: " . $migration_err->getMessage());
    }

    // Ensure inquiry whatsapp welcome automation exists
    try {
        $stmt_check_wa = $pdo->prepare("SELECT COUNT(*) FROM automations WHERE trigger_event = ? AND action_type = ?");
        $stmt_check_wa->execute(['inquiry_created', 'send_whatsapp']);
        if ($stmt_check_wa->fetchColumn() == 0) {
            $pdo->exec("
                INSERT INTO automations (name, trigger_event, action_type, action_payload, is_active)
                VALUES ('Auto WhatsApp Welcome Notification', 'inquiry_created', 'send_whatsapp', '{\"message\": \"Hi {name}, thank you for contacting RABSS Technologies! We have received your inquiry regarding {project_type}. Our CEO Subash Sitaula will follow up shortly.\"}', 1);
            ");
        }
    } catch (PDOException $migration_err) {
        error_log("Failed to seed whatsapp welcome automation: " . $migration_err->getMessage());
    }

    // Automatic Schema Migration: Verify older databases have 'source' column inside inquiries table
    try {
        $stmt_pragma = $pdo->query("PRAGMA table_info(inquiries)");
        $columns = $stmt_pragma->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!empty($columns) && !in_array('source', $columns)) {
            $pdo->exec("ALTER TABLE inquiries ADD COLUMN source TEXT DEFAULT 'Website Form'");
        }
    } catch (PDOException $migration_err) {
        error_log("Database migration failed: " . $migration_err->getMessage());
    }

    try {
        $stmt_pragma_leads = $pdo->query("PRAGMA table_info(leads)");
        $columns_leads = $stmt_pragma_leads->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!empty($columns_leads) && !in_array('phone', $columns_leads)) {
            $pdo->exec("ALTER TABLE leads ADD COLUMN phone TEXT");
        }
        if (!empty($columns_leads) && !in_array('source', $columns_leads)) {
            $pdo->exec("ALTER TABLE leads ADD COLUMN source TEXT DEFAULT 'Outreach'");
        }
    } catch (PDOException $migration_err) {
        error_log("Database migration failed: " . $migration_err->getMessage());
    }

} catch (PDOException $e) {
    if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/') || (defined('API_REQUEST') && API_REQUEST)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
    throw $e;
}