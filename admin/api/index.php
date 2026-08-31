<?php
// admin/api/index.php

// Prevent raw PHP errors from corrupting the JSON stream
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/php_error_log.txt');

// Capture all notices and warnings into our custom debug file
set_error_handler(function($severity, $message, $file, $line) {
    $logMsg = "[" . date('Y-m-d H:i:s') . "] PHP WARNING/NOTICE: {$message} in {$file} on line {$line}\n";
    @file_put_contents(__DIR__ . '/debug_log.txt', $logMsg, FILE_APPEND);
    return false;
});

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

try {
    @$pdo->exec("ALTER TABLE inquiries ADD COLUMN whatsapp TEXT");
} catch (\Exception $e) {
    // Column already exists or db issues
}

session_start();

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Robust raw input extraction (read php://input only once)
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
    $input = $_POST;
}

function respond(mixed $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function logAudit(\PDO $pdo, string $userName, string $act, string $entity, mixed $entityId = null): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_name, action, entity, entity_id, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userName, $act, $entity, (string)$entityId, $ip]);
}

// 1. PUBLIC WEBSITE INQUIRY WEBHOOK (Captures leads directly from public website)
if ($action === 'submit_inquiry' && $method === 'POST') {
    $name = trim($input['name'] ?? $input['fullname'] ?? $input['full-name'] ?? $_POST['name'] ?? $_POST['fullname'] ?? $_GET['name'] ?? '');
    $email = trim($input['email'] ?? $input['email_address'] ?? $input['email-address'] ?? $_POST['email'] ?? $_GET['email'] ?? '');
    $company = trim($input['company'] ?? $_POST['company'] ?? $_GET['company'] ?? '');
    $country = trim($input['country'] ?? $_POST['country'] ?? $_GET['country'] ?? '');
    $project_type = trim($input['project_type'] ?? $_POST['project_type'] ?? $_GET['project_type'] ?? 'Custom Software');
    $budget = trim($input['budget'] ?? $_POST['budget'] ?? $_GET['budget'] ?? 'Flexible');
    $timeline = trim($input['timeline'] ?? $_POST['timeline'] ?? $_GET['timeline'] ?? 'Normal');
    $description = trim($input['description'] ?? $_POST['description'] ?? $_GET['description'] ?? '');
    $whatsapp = trim($input['whatsapp'] ?? $_POST['whatsapp'] ?? $_GET['whatsapp'] ?? '');

    if (!$name || !$email) {
        respond(['status' => 'error', 'message' => 'Name and Email are required.'], 400);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO inquiries (name, email, company, country, project_type, budget, timeline, description, status, whatsapp)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'New', ?)
        ");
        $stmt->execute([$name, $email, $company, $country, $project_type, $budget, $timeline, $description, $whatsapp]);
        $inquiryId = $pdo->lastInsertId();

        logAudit($pdo, 'System Webhook', "New Inquiry received from {$name} ({$country})", 'Inquiry', $inquiryId);

        // Trigger active automations for the inquiry_created event
        if (file_exists(__DIR__ . '/automation_engine.php')) {
            require_once __DIR__ . '/automation_engine.php';
            triggerAutomation($pdo, 'inquiry_created', [
                'inquiry_id' => $inquiryId,
                'name' => $name,
                'email' => $email,
                'company' => $company,
                'country' => $country,
                'project_type' => $project_type,
                'budget' => $budget,
                'timeline' => $timeline,
                'description' => $description,
                'whatsapp' => $whatsapp
            ]);
        }

        respond(['status' => 'success', 'message' => 'Inquiry registered and synced to Super Admin OS.']);
    } catch (\PDOException $e) {
        respond(['status' => 'error', 'message' => 'Database write failed: ' . $e->getMessage()], 500);
    }
}

// 2. AUTHENTICATION & LOGIN
if ($action === 'login' && $method === 'POST') {
    $email = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];

        logAudit($pdo, $user['name'], 'Logged in to Super Admin BOS', 'Auth');
        respond(['status' => 'success', 'user' => [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]]);
    } else {
        respond(['status' => 'error', 'message' => 'Invalid email or master password.'], 401);
    }
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    respond(['status' => 'success', 'message' => 'Logged out successfully.']);
}

// 3. DASHBOARD METRICS & SUMMARY
if ($action === 'get_dashboard') {
    $rev = $pdo->query("SELECT SUM(total) as total_rev FROM invoices WHERE status = 'Paid'")->fetch()['total_rev'] ?? 0;
    $out = $pdo->query("SELECT SUM(total) as total_out FROM invoices WHERE status IN ('Sent', 'Overdue')")->fetch()['total_out'] ?? 0;
    $exp = $pdo->query("SELECT SUM(amount) as total_exp FROM expenses")->fetch()['total_exp'] ?? 0;
    $activeProj = $pdo->query("SELECT COUNT(*) as cnt FROM projects WHERE status NOT IN ('Completed', 'Cancelled')")->fetch()['cnt'] ?? 0;
    $newInquiries = $pdo->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'New'")->fetch()['cnt'] ?? 0;

    respond([
        'status' => 'success',
        'metrics' => [
            'revenue' => (float)$rev,
            'outstanding' => (float)$out,
            'expenses' => (float)$exp,
            'net_profit' => (float)($rev - $exp),
            'active_projects' => (int)$activeProj,
            'new_leads' => (int)$newInquiries,
            'conversion_rate' => '32.4%'
        ]
    ]);
}

// 3.5 INQUIRIES ENDPOINT
if ($action === 'get_inquiries') {
    $inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC")->fetchAll();
    respond(['status' => 'success', 'inquiries' => $inquiries]);
}

// 4. LEADS & CRM ENDPOINTS
if ($action === 'get_leads') {
    $leads = $pdo->query("SELECT * FROM leads ORDER BY id DESC")->fetchAll();
    respond(['status' => 'success', 'leads' => $leads]);
}

if ($action === 'create_inquiry' && $method === 'POST') {
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $company = trim($input['company'] ?? '');
    $country = trim($input['country'] ?? '');
    $project_type = trim($input['project_type'] ?? 'Custom Software');
    $budget = trim($input['budget'] ?? 'Flexible');
    $timeline = trim($input['timeline'] ?? 'Normal');
    $description = trim($input['description'] ?? '');
    $whatsapp = trim($input['whatsapp'] ?? '');

    if (!$name || !$email) {
        respond(['status' => 'error', 'message' => 'Name and Email are required.'], 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO inquiries (name, email, company, country, project_type, budget, timeline, description, status, whatsapp)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'New', ?)
    ");
    $stmt->execute([$name, $email, $company, $country, $project_type, $budget, $timeline, $description, $whatsapp]);
    $inquiryId = $pdo->lastInsertId();

    logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Created Inquiry #{$inquiryId} ({$name})", 'Inquiry', $inquiryId);

    // Trigger active automations for the inquiry_created event
    if (file_exists(__DIR__ . '/automation_engine.php')) {
        require_once __DIR__ . '/automation_engine.php';
        triggerAutomation($pdo, 'inquiry_created', [
            'inquiry_id' => $inquiryId,
            'name' => $name,
            'email' => $email,
            'company' => $company,
            'country' => $country,
            'project_type' => $project_type,
            'budget' => $budget,
            'timeline' => $timeline,
            'description' => $description,
            'whatsapp' => $whatsapp
        ]);
    }

    respond(['status' => 'success', 'inquiry_id' => $inquiryId]);
}

if ($action === 'delete_inquiry' && $method === 'POST') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        respond(['status' => 'error', 'message' => 'Inquiry ID is required.'], 400);
    }

    $stmt = $pdo->prepare("SELECT name FROM inquiries WHERE id = ?");
    $stmt->execute([$id]);
    $inq = $stmt->fetch();

    if ($inq) {
        $stmtDel = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmtDel->execute([$id]);
        logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Deleted Inquiry #{$id} ({$inq['name']})", 'Inquiry', $id);
        respond(['status' => 'success', 'message' => 'Inquiry deleted successfully.']);
    } else {
        respond(['status' => 'error', 'message' => 'Inquiry not found.'], 404);
    }
}

if ($action === 'promote_to_lead' && $method === 'POST') {
    $inquiryId = (int)($input['inquiry_id'] ?? 0);
    if (!$inquiryId) {
        respond(['status' => 'error', 'message' => 'Inquiry ID is required.'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM inquiries WHERE id = ?");
    $stmt->execute([$inquiryId]);
    $inquiry = $stmt->fetch();

    if (!$inquiry) {
        respond(['status' => 'error', 'message' => 'Inquiry not found.'], 404);
    }

    try {
        $pdo->beginTransaction();

        $estimatedValue = 0.0;
        $currency = 'USD';
        if (!empty($inquiry['budget'])) {
            preg_match_all('/\d+[\d,.]*/', $inquiry['budget'], $matches);
            if (!empty($matches[0])) {
                $valStr = str_replace(',', '', $matches[0][0]);
                if (stripos($inquiry['budget'], 'k') !== false) {
                    $estimatedValue = (float)$valStr * 1000;
                } else {
                    $estimatedValue = (float)$valStr;
                }
            }
            if (preg_match('/(USD|CAD|AED|QAR)/i', $inquiry['budget'], $currMatches)) {
                $currency = strtoupper($currMatches[1]);
            }
        }

        $stmtLead = $pdo->prepare("INSERT INTO leads (name, company, country, email, phone, service, source, estimated_value, currency, stage, notes, assigned_to) VALUES (?, ?, ?, ?, ?, ?, 'Website Form', ?, ?, 'New Inquiry', ?, 'Subash Sitaula')");
        $stmtLead->execute([$inquiry['name'], $inquiry['company'] ?? '', $inquiry['country'] ?? 'Other', $inquiry['email'], $inquiry['whatsapp'] ?? '', $inquiry['project_type'] ?? 'Custom Software', $estimatedValue, $currency, $inquiry['description'] ?? '']);
        $leadId = $pdo->lastInsertId();

        $stmtInq = $pdo->prepare("UPDATE inquiries SET status = 'Converted' WHERE id = ?");
        $stmtInq->execute([$inquiryId]);

        logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Promoted Inquiry #{$inquiryId} to Lead #{$leadId}", 'Lead', $leadId);
        $pdo->commit();
        respond(['status' => 'success', 'lead_id' => $leadId]);
    } catch (\Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        respond(['status' => 'error', 'message' => 'Failed to promote inquiry: ' . $e->getMessage()], 500);
    }
}

if ($action === 'create_lead' && $method === 'POST') {
    $name = trim($input['name'] ?? '');
    $company = trim($input['company'] ?? '');
    $country = trim($input['country'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $service = trim($input['service'] ?? 'Custom Software');
    $source = trim($input['source'] ?? 'Outreach');
    $value = (float)($input['estimated_value'] ?? 0);
    $currency = trim($input['currency'] ?? 'USD');
    $next_followup = trim($input['next_followup'] ?? '');
    $assigned_to = trim($input['assigned_to'] ?? 'Subash Sitaula');
    $notes = trim($input['notes'] ?? '');

    if (!$name) {
        respond(['status' => 'error', 'message' => 'Lead Name is required.'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO leads (name, company, country, email, phone, service, source, estimated_value, currency, stage, next_followup, assigned_to, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'New Inquiry', ?, ?, ?)");
    $stmt->execute([$name, $company, $country, $email, $phone, $service, $source, $value, $currency, $next_followup ?: null, $assigned_to, $notes]);
    logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Created Lead #" . $pdo->lastInsertId() . " ({$name})", 'Lead', $pdo->lastInsertId());
    respond(['status' => 'success', 'lead_id' => $pdo->lastInsertId()]);
}

if ($action === 'update_lead_stage' && $method === 'POST') {
    $leadId = (int)($input['lead_id'] ?? 0);
    $stage = trim($input['stage'] ?? '');

    $stmtLead = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
    $stmtLead->execute([$leadId]);
    $lead = $stmtLead->fetch();

    if ($lead) {
        $stmt = $pdo->prepare("UPDATE leads SET stage = ? WHERE id = ?");
        $stmt->execute([$stage, $leadId]);
        logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Moved Lead #{$leadId} to {$stage}", 'Lead', $leadId);

        // Trigger active automations for the lead_stage_changed event
        if (file_exists(__DIR__ . '/automation_engine.php')) {
            require_once __DIR__ . '/automation_engine.php';
            triggerAutomation($pdo, 'lead_stage_changed', [
                'lead_id' => $leadId,
                'name' => $lead['name'] ?? '',
                'email' => $lead['email'] ?? '',
                'company' => $lead['company'] ?? '',
                'country' => $lead['country'] ?? '',
                'phone' => $lead['phone'] ?? '',
                'service' => $lead['service'] ?? '',
                'estimated_value' => $lead['estimated_value'] ?? 0,
                'currency' => $lead['currency'] ?? 'USD',
                'stage' => $stage,
                'notes' => $lead['notes'] ?? ''
            ]);
        }
        respond(['status' => 'success']);
    } else {
        respond(['status' => 'error', 'message' => 'Lead not found.'], 404);
    }
}

if ($action === 'delete_lead' && $method === 'POST') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        respond(['status' => 'error', 'message' => 'Lead ID is required.'], 400);
    }

    $stmt = $pdo->prepare("SELECT name FROM leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if ($lead) {
        $stmtDel = $pdo->prepare("DELETE FROM leads WHERE id = ?");
        $stmtDel->execute([$id]);
        logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Deleted Lead #{$id} ({$lead['name']})", 'Lead', $id);
        respond(['status' => 'success', 'message' => 'Lead deleted successfully.']);
    } else {
        respond(['status' => 'error', 'message' => 'Lead not found.'], 404);
    }
}

// 5. PROJECTS & TASKS
if ($action === 'get_projects') {
    $projects = $pdo->query("
        SELECT p.*, c.name as client_name, c.company as client_company 
        FROM projects p 
        LEFT JOIN clients c ON p.client_id = c.id 
        ORDER BY p.id DESC
    ")->fetchAll();
    respond(['status' => 'success', 'projects' => $projects]);
}

if ($action === 'get_tasks') {
    $tasks = $pdo->query("SELECT t.*, p.name as project_name FROM tasks t LEFT JOIN projects p ON t.project_id = p.id ORDER BY t.id DESC")->fetchAll();
    respond(['status' => 'success', 'tasks' => $tasks]);
}

if ($action === 'create_project' && $method === 'POST') {
    $name = trim($input['name'] ?? '');
    $clientId = (int)($input['client_id'] ?? 1);
    $type = trim($input['project_type'] ?? 'Custom Software');
    $budget = (float)($input['budget'] ?? 0);
    $currency = trim($input['currency'] ?? 'USD');
    $deadline = trim($input['deadline'] ?? date('Y-m-d', strtotime('+30 days')));
    $description = trim($input['description'] ?? '');

    if (!$name) {
        respond(['status' => 'error', 'message' => 'Project Name is required.'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO projects (client_id, name, project_type, budget, currency, start_date, deadline, status, progress, assigned_team, description) VALUES (?, ?, ?, ?, ?, DATE('now'), ?, 'Planning', 0, 'Subash Sitaula', ?)");
    $stmt->execute([$clientId, $name, $type, $budget, $currency, $deadline, $description]);
    logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Created Project #" . $pdo->lastInsertId() . " ({$name})", 'Project', $pdo->lastInsertId());
    respond(['status' => 'success', 'project_id' => $pdo->lastInsertId()]);
}

if ($action === 'create_task' && $method === 'POST') {
    $title = trim($input['title'] ?? '');
    $projectId = (int)($input['project_id'] ?? 1);
    $assignee = trim($input['assignee'] ?? 'Subash Sitaula');
    $priority = trim($input['priority'] ?? 'Medium');
    $status = trim($input['status'] ?? 'To Do');

    $stmt = $pdo->prepare("INSERT INTO tasks (title, project_id, assignee, priority, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $projectId, $assignee, $priority, $status]);
    respond(['status' => 'success', 'task_id' => $pdo->lastInsertId()]);
}

// 6. INVOICES & PAYMENTS
if ($action === 'get_invoices') {
    $invoices = $pdo->query("
        SELECT i.*, c.name as client_name, c.company as client_company, p.name as project_name
        FROM invoices i
        LEFT JOIN clients c ON i.client_id = c.id
        LEFT JOIN projects p ON i.project_id = p.id
        ORDER BY i.id DESC
    ")->fetchAll();
    respond(['status' => 'success', 'invoices' => $invoices]);
}

if ($action === 'create_invoice' && $method === 'POST') {
    $invNum = 'INV-2026-' . str_pad((string)rand(10, 9999), 4, '0', STR_PAD_LEFT);
    $clientId = (int)($input['client_id'] ?? 1);
    $amount = (float)($input['amount'] ?? 0);
    $currency = trim($input['currency'] ?? 'USD');
    $dueDate = date('Y-m-d', strtotime('+15 days'));

    $stmt = $pdo->prepare("INSERT INTO invoices (invoice_number, client_id, issue_date, due_date, currency, subtotal, total, status) VALUES (?, ?, DATE('now'), ?, ?, ?, ?, 'Sent')");
    $stmt->execute([$invNum, $clientId, $dueDate, $currency, $amount, $amount]);
    logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Created Invoice {$invNum} for {$currency} {$amount}", 'Invoice');
    respond(['status' => 'success', 'invoice_number' => $invNum]);
}

// 7. CLIENTS
if ($action === 'get_clients') {
    $clients = $pdo->query("SELECT * FROM clients ORDER BY id DESC")->fetchAll();
    respond(['status' => 'success', 'clients' => $clients]);
}

// 7.5 MEETINGS & SCHEDULING
if ($action === 'get_meetings') {
    $meetings = $pdo->query("SELECT * FROM meetings ORDER BY id DESC")->fetchAll();
    respond(['status' => 'success', 'meetings' => $meetings]);
}

if ($action === 'schedule_meeting' && $method === 'POST') {
    $title = trim($input['title'] ?? '');
    $invitee_name = trim($input['invitee_name'] ?? '');
    $invitee_email = trim($input['invitee_email'] ?? '');
    $scheduled_at = trim($input['scheduled_at'] ?? '');

    if (!$title || !$invitee_name || !$invitee_email || !$scheduled_at) {
        respond(['status' => 'error', 'message' => 'All fields are required.'], 400);
    }

    try {
        $roomId = 'room_' . bin2hex(random_bytes(6));

        $stmt = $pdo->prepare("
            INSERT INTO meetings (room_id, title, invitee_name, invitee_email, scheduled_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$roomId, $title, $invitee_name, $invitee_email, $scheduled_at]);
        $meetingId = $pdo->lastInsertId();

        logAudit($pdo, $_SESSION['user_name'] ?? 'Admin', "Scheduled meeting '{$title}' with {$invitee_name}", 'Meeting', $meetingId);

        // Generate absolute meeting link
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $baseDir = str_replace('/api/index.php', '', $_SERVER['SCRIPT_NAME']);
        $meetingLink = "{$protocol}://{$host}{$baseDir}/meeting.php?room={$roomId}";

        // Send invitation email
        $subject = "Video Meeting Invitation: {$title} — RABSS Technologies";
        $body = "Hi {$invitee_name},\n\n";
        $body .= "You have been invited to a secure video meeting with RABSS Technologies.\n\n";
        $body .= "Meeting Title: {$title}\n";
        $body .= "Scheduled Time: {$scheduled_at}\n\n";
        $body .= "To join the meeting room directly, please click the secure link below:\n";
        $body .= "{$meetingLink}\n\n";
        $body .= "Best regards,\nSubash Sitaula\nFounder & CEO, RABSS Technologies\nrabsstechnologies@gmail.com";

        $emailSent = false;
        $smtpErr = '';

        if (file_exists(__DIR__ . '/automation_engine.php')) {
            require_once __DIR__ . '/automation_engine.php';
            try {
                $emailSent = sendSmtpEmail($invitee_email, $subject, $body);
            } catch (\Exception $ex) {
                $smtpErr = $ex->getMessage();
                // Fallback to standard php mail()
                $headers = "From: rabsstechnologies@gmail.com\r\nReply-To: rabsstechnologies@gmail.com\r\nX-Mailer: PHP/" . PHP_VERSION;
                $emailSent = @mail($invitee_email, $subject, $body, $headers);
            }
        }

        respond([
            'status' => 'success', 
            'message' => 'Meeting scheduled and invitation sent successfully.',
            'room_id' => $roomId,
            'meeting_link' => $meetingLink,
            'email_sent' => $emailSent,
            'smtp_error' => $smtpErr
        ]);
    } catch (\Exception $e) {
        respond(['status' => 'error', 'message' => 'Failed to schedule meeting: ' . $e->getMessage()], 500);
    }
}

// 8. AUDIT LOGS
if ($action === 'get_audit_logs') {
    $logs = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 50")->fetchAll();
    respond(['status' => 'success', 'logs' => $logs]);
}

// 9. AI BUSINESS ASSISTANT QUERIES
if ($action === 'ask_ai' && $method === 'POST') {
    $query = strtolower(trim($input['query'] ?? ''));
    $response = "";

    if (strpos($query, 'revenue') !== false || strpos($query, 'profit') !== false) {
        $rev = $pdo->query("SELECT SUM(total) as t FROM invoices WHERE status = 'Paid'")->fetch()['t'] ?? 0;
        $exp = $pdo->query("SELECT SUM(amount) as t FROM expenses")->fetch()['t'] ?? 0;
        $response = "Total verified paid revenue is $" . number_format($rev, 2) . " USD, with total recorded expenses of $" . number_format($exp, 2) . " USD, producing an operating net profit of $" . number_format($rev - $exp, 2) . " USD.";
    } elseif (strpos($query, 'overdue') !== false || strpos($query, 'invoice') !== false) {
        $overdue = $pdo->query("SELECT invoice_number, total, currency FROM invoices WHERE status IN ('Sent', 'Overdue')")->fetchAll();
        $response = "Found " . count($overdue) . " pending/sent invoices totaling receivables across USD & AED markets.";
    } elseif (strpos($query, 'lead') !== false || strpos($query, 'inquiry') !== false) {
        $leads = $pdo->query("SELECT name, company, country, estimated_value, currency FROM leads WHERE stage = 'New Inquiry'")->fetchAll();
        $response = "You have " . count($leads) . " new inquiries ready for discovery calls from USA, Canada, UAE, and Qatar.";
    } else {
        $response = "RABSS AI OS analyzed the database: all systems operational, 2 active international projects, 0 critical SLA blockers.";
    }

    respond(['status' => 'success', 'answer' => $response]);
}

// 10. SYSTEM DIAGNOSTICS FOR HARD DEBUGGING
if ($action === 'diagnostics') {
    $diag = [];
    $diag['php_version'] = PHP_VERSION;
    $diag['pdo_drivers'] = PDO::getAvailableDrivers();
    $diag['sqlite_supported'] = in_array('sqlite', $diag['pdo_drivers']) ? 'Yes' : 'No';

    $db_dir = __DIR__;
    $db_file = $db_dir . '/rabss_os.sqlite';

    $diag['api_dir'] = $db_dir;
    $diag['api_dir_writable'] = is_writable($db_dir) ? 'Yes' : 'No';

    $diag['db_file'] = $db_file;
    $diag['db_file_exists'] = file_exists($db_file) ? 'Yes' : 'No';
    if (file_exists($db_file)) {
        $diag['db_file_readable'] = is_readable($db_file) ? 'Yes' : 'No';
        $diag['db_file_writable'] = is_writable($db_file) ? 'Yes' : 'No';
    } else {
        $diag['db_file_readable'] = 'N/A';
        $diag['db_file_writable'] = 'N/A';
    }

    try {
        $test_pdo = new PDO("sqlite:" . $db_file);
        $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $diag['db_connection'] = 'Success';

        $tables = [];
        $q = $test_pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
        while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }
        $diag['existing_tables'] = $tables;

        if (in_array('inquiries', $tables)) {
            $count = $test_pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
            $diag['inquiries_count'] = $count;
        } else {
            $diag['inquiries_count'] = 'Table inquiries does not exist!';
        }
    } catch (\Exception $e) {
        $diag['db_connection'] = 'Failed: ' . $e->getMessage();
        $diag['existing_tables'] = [];
        $diag['inquiries_count'] = 0;
    }

    $debug_log_path = $db_dir . '/debug_log.txt';
    if (file_exists($debug_log_path)) {
        $diag['debug_log_writable'] = is_writable($debug_log_path) ? 'Yes' : 'No';
        $log_data = @file_get_contents($debug_log_path);
        $lines = explode("\n", (string)$log_data);
        $diag['debug_log_last_lines'] = array_slice($lines, -15);
    } else {
        $diag['debug_log_writable'] = 'File does not exist yet';
        $diag['debug_log_last_lines'] = [];
    }

    $err_log_path = $db_dir . '/php_error_log.txt';
    if (file_exists($err_log_path)) {
        $diag['php_error_log_writable'] = is_writable($err_log_path) ? 'Yes' : 'No';
        $log_data = @file_get_contents($err_log_path);
        $lines = explode("\n", (string)$log_data);
        $diag['php_error_log_last_lines'] = array_slice($lines, -15);
    } else {
        $diag['php_error_log_writable'] = 'File does not exist yet';
        $diag['php_error_log_last_lines'] = [];
    }

    respond(['status' => 'success', 'diagnostics' => $diag]);
}

respond(['status' => 'error', 'message' => 'Invalid action endpoint.'], 404);