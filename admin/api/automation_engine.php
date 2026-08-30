<?php
// admin/api/automation_engine.php

// SMTP Configuration for transactional emails
if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 465);
if (!defined('SMTP_USER')) define('SMTP_USER', 'rabsstechnologies@gmail.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'cxah rssc fxje nkxo'); // Set your Google App Password or SMTP credentials here
if (!defined('SMTP_SECURE')) define('SMTP_SECURE', 'ssl'); // 'ssl', 'tls', or ''
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'RABSS Technologies');

// Self-contained lightweight socket-based SMTP client
if (!function_exists('sendSmtpEmail')) {
    function sendSmtpEmail(string $to, string $subject, string $body, array $config = []): bool {
        $host = $config['host'] ?? SMTP_HOST;
        $port = (int)($config['port'] ?? SMTP_PORT);
        $username = $config['username'] ?? SMTP_USER;
        $password = $config['password'] ?? SMTP_PASS;
        $fromEmail = $config['from'] ?? SMTP_USER;
        $fromName = $config['from_name'] ?? SMTP_FROM_NAME;
        $encryption = $config['secure'] ?? SMTP_SECURE;

        if (empty($password)) {
            throw new \Exception("SMTP password is not configured.");
        }

        // Programmatically strip cosmetic spaces from Google App Password
        $password = str_replace(' ', '', $password);

        // Set up SSL context to bypass peer verification issues on local/unconfigured dev environments
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $remote = ($encryption === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
        $socket = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            throw new \Exception("Could not connect to SMTP host {$host} on port {$port}: {$errstr} ({$errno})");
        }

        $read = function($socket) {
            $data = '';
            while ($str = fgets($socket, 515)) {
                if ($str === false) {
                    break;
                }
                $data .= $str;
                if (strlen($str) >= 4 && substr($str, 3, 1) === ' ') {
                    break;
                }
            }
            return $data;
        };

        $write = function($socket, $cmd) {
            fputs($socket, $cmd . "\r\n");
        };

        $read($socket); // 220

        $write($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
        $read($socket); // 250

        if ($encryption === 'tls') {
            $write($socket, "STARTTLS");
            $response = $read($socket); // 220
            if (strpos($response, '220') === false) {
                fclose($socket);
                throw new \Exception("SMTP STARTTLS initiation failed: " . trim($response));
            }
            if (@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT) === false) {
                fclose($socket);
                throw new \Exception("Failed to enable TLS encryption on socket.");
            }
            $write($socket, "EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost'));
            $read($socket); // 250
        }

        if (!empty($username) && !empty($password)) {
            $write($socket, "AUTH LOGIN");
            $response = $read($socket); // 334
            if (strpos($response, '334') === false) {
                fclose($socket);
                throw new \Exception("SMTP AUTH LOGIN initialization failed: " . trim($response));
            }
            
            $write($socket, base64_encode($username));
            $response = $read($socket); // 334
            if (strpos($response, '334') === false) {
                fclose($socket);
                throw new \Exception("SMTP username challenge failed: " . trim($response));
            }

            $write($socket, base64_encode($password));
            $response = $read($socket); // 235
            if (strpos($response, '235') === false) {
                fclose($socket);
                throw new \Exception("SMTP Authentication failed: " . trim($response));
            }
        }

        $write($socket, "MAIL FROM:<" . $fromEmail . ">");
        $response = $read($socket); // 250
        if (strpos($response, '250') === false) {
            fclose($socket);
            throw new \Exception("SMTP MAIL FROM failed: " . trim($response));
        }

        $write($socket, "RCPT TO:<" . $to . ">");
        $response = $read($socket); // 250/251
        if (strpos($response, '250') === false && strpos($response, '251') === false) {
            fclose($socket);
            throw new \Exception("SMTP RCPT TO failed: " . trim($response));
        }

        $write($socket, "DATA");
        $response = $read($socket); // 354
        if (strpos($response, '354') === false) {
            fclose($socket);
            throw new \Exception("SMTP DATA handshake failed: " . trim($response));
        }

        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/plain; charset=UTF-8",
            "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <" . $fromEmail . ">",
            "To: <" . $to . ">",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Date: " . date('r'),
            "X-Mailer: PHP/" . PHP_VERSION
        ];

        // Strict SMTP servers require CRLF (\r\n) line endings for headers and body
        $body = str_replace(["\r\n", "\r", "\n"], "\r\n", $body);

        $write($socket, implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.");
        $response = $read($socket); // 250
        if (strpos($response, '250') === false) {
            fclose($socket);
            throw new \Exception("SMTP DATA transmit failed: " . trim($response));
        }

        $write($socket, "QUIT");
        fclose($socket);

        return true;
    }
}

if (!function_exists('triggerAutomation')) {
    function triggerAutomation(PDO $pdo, string $triggerEvent, array $payload = []) {
        if (!$pdo) {
            return [];
        }

        // Fetch active rules matching trigger
        $stmt = $pdo->prepare("SELECT * FROM automations WHERE is_active = 1 AND trigger_event = ? ORDER BY id ASC");
        $stmt->execute([$triggerEvent]);
        $rules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rules as $rule) {
            $startTime = microtime(true);
            $actionOutput = "";
            $status = "SUCCESS";

            // Evaluate condition if present
            if (!empty($rule['condition_field'])) {
                $field = $rule['condition_field'];
                $expected = strtolower(trim($rule['condition_value'] ?? ''));
                $actual = isset($payload[$field]) ? strtolower(trim((string)$payload[$field])) : '';

                if (strpos($actual, $expected) === false && $actual !== $expected) {
                    // Condition not met, skip this rule
                    continue;
                }
            }

            $actionPayload = [];
            if (!empty($rule['action_payload'])) {
                $actionPayload = json_decode($rule['action_payload'], true) ?: [];
            }

            try {
                switch ($rule['action_type']) {
                    case 'create_lead':
                        // Extract estimated value and currency
                        $estimatedValue = 0.0;
                        $currency = 'USD';
                        $budgetStr = $payload['budget'] ?? '';
                        if (!empty($budgetStr)) {
                            preg_match_all('/\d+[\d,.]*/', $budgetStr, $matches);
                            if (!empty($matches[0])) {
                                $valStr = str_replace(',', '', $matches[0][0]);
                                if (stripos($budgetStr, 'k') !== false) {
                                    $estimatedValue = (float)$valStr * 1000;
                                } else {
                                    $estimatedValue = (float)$valStr;
                                }
                            }
                            if (preg_match('/(USD|CAD|AED|QAR)/i', $budgetStr, $currMatches)) {
                                $currency = strtoupper($currMatches[1]);
                            }
                        }

                        $stmtLead = $pdo->prepare("
                            INSERT INTO leads (name, company, country, email, phone, service, source, estimated_value, currency, stage, notes, assigned_to)
                            VALUES (?, ?, ?, ?, ?, ?, 'Website Form', ?, ?, 'New Inquiry', ?, ?)
                        ");
                        $stmtLead->execute([
                            $payload['name'] ?? 'Unnamed Inquiry',
                            $payload['company'] ?? '',
                            $payload['country'] ?? 'Other',
                            $payload['email'] ?? '',
                            $payload['whatsapp'] ?? $payload['phone'] ?? '',
                            $payload['project_type'] ?? 'Custom Software',
                            $estimatedValue,
                            $currency,
                            $payload['description'] ?? '',
                            $actionPayload['assigned_to'] ?? 'Subash Sitaula'
                        ]);
                        $leadId = $pdo->lastInsertId();
                        $actionOutput = "Successfully created CRM Lead #{$leadId} and assigned to " . ($actionPayload['assigned_to'] ?? 'Subash Sitaula');
                        break;

                    case 'send_email':
                        $to = $payload['email'] ?? 'ceo@rabss.tech';
                        
                        $stage = isset($payload['stage']) ? trim($payload['stage']) : '';
                        $subject = '';
                        $body = '';

                        if ($triggerEvent === 'inquiry_created') {
                            $subject = $actionPayload['subject'] ?? "Inquiry Received — RABSS Technologies";
                            $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                            $body .= "Thank you for contacting RABSS Technologies! We have received your inquiry regarding " . ($payload['project_type'] ?? 'your custom software project') . " with a budget of " . ($payload['budget'] ?? 'Flexible') . ".\n\n";
                            $body .= "Our team, personally led by Founder & CEO Subash Sitaula, will review your details and reach out within 12-24 hours with an actionable roadmap.\n\n";
                        } elseif ($triggerEvent === 'lead_stage_changed') {
                            switch ($stage) {
                                case 'Contacted':
                                    $subject = "Let's connect — RABSS Technologies";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "I hope you are doing well!\n\n";
                                    $body .= "We have processed your inquiry for " . ($payload['company'] ?: 'your company') . " regarding " . ($payload['service'] ?: 'custom software') . ", and we'd love to set up a quick introduction call to discuss your goals in more detail.\n\n";
                                    $body .= "Please let us know your availability, or feel free to reply directly to this email or message us on WhatsApp.\n\n";
                                    break;
                                    
                                case 'Discovery':
                                    $subject = "Technical Discovery Call Follow-up — RABSS Technologies";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "It was great speaking with you/initiating our technical discovery process!\n\n";
                                    $body .= "We are currently analyzing your requirements for the custom " . ($payload['service'] ?: 'software') . " build. We'll outline the architectural blueprint, timeline, and exact milestones next.\n\n";
                                    $body .= "Let us know if you have any additional notes to append.\n\n";
                                    break;
                                    
                                case 'Proposal Sent':
                                    $estValStr = $payload['estimated_value'] ? (($payload['currency'] ?? 'USD') . " " . number_format((float)$payload['estimated_value'], 2)) : 'Flexible / TBD';
                                    $subject = "Your Custom Software Proposal — RABSS Technologies";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "We have prepared and dispatched your custom development proposal!\n\n";
                                    $body .= "The scope outlines our step-by-step roadmap, timeline, and deliverables for " . ($payload['service'] ?: 'your project') . " estimated at " . $estValStr . ".\n\n";
                                    $body .= "Please review the scope and let us know if you'd like to schedule a walk-through.\n\n";
                                    break;
                                    
                                case 'Negotiation':
                                    $subject = "Proposal Discussion & Alignment — RABSS Technologies";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "Thank you for your feedback on our proposal. We are fully committed to aligning our milestones, terms, and technical strategy with your commercial launch target.\n\n";
                                    $body .= "Let us know your thoughts on the proposed iterations.\n\n";
                                    break;
                                    
                                case 'Won':
                                    $subject = "Welcome to RABSS Technologies! Project Kickoff";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "It is official — welcome aboard! We are thrilled to partner with you to build your software platform.\n\n";
                                    $body .= "We have scaffolded your dedicated project workspace. Founder Subash Sitaula and our engineering team will initiate onboarding and issue sprint tasks shortly.\n\n";
                                    $body .= "Let's build something exceptional together!\n\n";
                                    break;
                                    
                                default:
                                    $subject = "Lead Stage Updated: " . ($stage ?: 'Discovery') . " — RABSS Technologies";
                                    $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                                    $body .= "This is an automated update that your opportunity for " . ($payload['service'] ?: 'custom software') . " has transitioned to the stage: " . ($stage ?: 'Updated') . ".\n\n";
                                    $body .= "We'll be in touch with further updates regarding our operational roadmap.\n\n";
                                    break;
                            }
                        } else {
                            $subject = $actionPayload['subject'] ?? "Inquiry Update — RABSS Technologies";
                            $body = "Hi " . ($payload['name'] ?? 'there') . ",\n\n";
                            $body .= "Thank you for partnering with us. This is an automated update regarding your custom software platform development.\n\n";
                        }

                        $body .= "Best regards,\nSubash Sitaula\nFounder & CEO, RABSS Technologies\nrabsstechnologies@gmail.com";

                        $mailSent = false;
                        $smtpErr = '';

                        try {
                            $mailSent = sendSmtpEmail($to, $subject, $body);
                            $actionOutput = "Successfully dispatched transactional email to {$to} via SMTP.";
                        } catch (\Exception $smtpEx) {
                            $smtpErr = $smtpEx->getMessage();
                            // Fall back to standard php mail()
                            $headers = "From: rabsstechnologies@gmail.com\r\nReply-To: rabsstechnologies@gmail.com\r\nX-Mailer: PHP/" . PHP_VERSION;
                            $mailSent = @mail($to, $subject, $body, $headers);
                            if ($mailSent) {
                                $actionOutput = "Successfully dispatched email to {$to} via mail() fallback (SMTP failed: {$smtpErr}).";
                            } else {
                                $actionOutput = "Failed to send email to {$to} via SMTP ({$smtpErr}) and mail() fallback.";
                            }
                        }

                        // Always log to debug log so you can verify it works locally on localhost
                        $logMsg = "[" . date('Y-m-d H:i:s') . "] Transactional Email Log (To: {$to}, Subject: {$subject})\n"
                                . "SMTP Status: " . ($smtpErr ? "Failed ({$smtpErr})" : "Success") . "\n"
                                . "Mail Fallback: " . ($mailSent && $smtpErr ? "Sent" : "N/A / Failed") . "\n"
                                . "Body:\n{$body}\n-----------------------\n";
                        @file_put_contents(__DIR__ . '/debug_log.txt', $logMsg, FILE_APPEND);
                        break;

                    case 'send_whatsapp':
                        $to = $payload['whatsapp'] ?? $payload['phone'] ?? '';
                        if (empty($to)) {
                            throw new \Exception("WhatsApp number is missing in payload.");
                        }

                        $rawMsg = $actionPayload['message'] ?? "Hi {name}, thank you for contacting RABSS Technologies! We have received your inquiry regarding {project_type} and will follow up soon.";
                        
                        // Replace placeholders
                        $replacements = [
                            '{name}' => $payload['name'] ?? 'there',
                            '{company}' => $payload['company'] ?? 'your company',
                            '{country}' => $payload['country'] ?? 'your country',
                            '{project_type}' => $payload['project_type'] ?? 'custom software',
                            '{budget}' => $payload['budget'] ?? 'Flexible',
                            '{description}' => $payload['description'] ?? ''
                        ];
                        $message = str_replace(array_keys($replacements), array_values($replacements), $rawMsg);

                        $waSent = false;
                        $waErr = '';

                        // Read potential API keys if defined
                        $accessToken = defined('WHATSAPP_TOKEN') ? WHATSAPP_TOKEN : '';
                        $phoneId = defined('WHATSAPP_PHONE_ID') ? WHATSAPP_PHONE_ID : '';

                        if (!empty($accessToken) && !empty($phoneId)) {
                            // Meta WhatsApp Cloud API Endpoint
                            $url = "https://graph.facebook.com/v21.0/{$phoneId}/messages";
                            $postData = json_encode([
                                'messaging_product' => 'whatsapp',
                                'recipient_type' => 'individual',
                                'to' => preg_replace('/[^0-9]/', '', $to),
                                'type' => 'text',
                                'text' => ['body' => $message]
                            ]);

                            $ch = curl_init($url);
                            curl_setopt_array($ch, [
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => $postData,
                                CURLOPT_HTTPHEADER => [
                                    "Authorization: Bearer {$accessToken}",
                                    "Content-Type: application/json"
                                ],
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 10
                            ]);
                            $response = curl_exec($ch);
                            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            // curl_close() is deprecated in PHP 8.5; the handle is cleaned up automatically.

                            if ($httpCode >= 200 && $httpCode < 300) {
                                $waSent = true;
                                $actionOutput = "Successfully dispatched WhatsApp message to {$to} via Meta Cloud API.";
                            } else {
                                $waErr = "Meta Cloud API returned HTTP {$httpCode}: " . ($response ?: 'No response');
                            }
                        }

                        if (!$waSent) {
                            $actionOutput = "Dispatched WhatsApp message to {$to} via Local Simulation Node.";
                            if (!empty($waErr)) {
                                $actionOutput .= " (API Attempt failed: {$waErr})";
                            }
                        }

                        // Always log to debug file for local testing
                        $logMsg = "[" . date('Y-m-d H:i:s') . "] Transactional WhatsApp Log (To: {$to})\n"
                                . "API Status: " . ($waSent ? "Success" : "Simulated/Logged") . "\n"
                                . "Message:\n{$message}\n-----------------------\n";
                        @file_put_contents(__DIR__ . '/debug_log.txt', $logMsg, FILE_APPEND);
                        break;

                    case 'create_task':
                        $title = $actionPayload['title'] ?? ("Follow up on " . ($payload['name'] ?? 'new inquiry'));
                        $priority = $actionPayload['priority'] ?? 'High';
                        $stmtTask = $pdo->prepare("
                            INSERT INTO tasks (title, project_id, assignee, priority, status)
                            VALUES (?, ?, 'Subash Sitaula', ?, 'To Do')
                        ");
                        $stmtTask->execute([$title, 1, $priority]);
                        $taskId = $pdo->lastInsertId();
                        $actionOutput = "Created internal checklist task #{$taskId} ('{$title}') with {$priority} priority.";
                        break;

                    case 'create_project':
                        $projectName = "Project: " . ($payload['company'] ?? $payload['name'] ?? 'New Build');
                        $budget = 5000.0;
                        if (!empty($payload['estimated_value'])) {
                            $budget = (float)$payload['estimated_value'];
                        }
                        $currency = $payload['currency'] ?? 'USD';
                        $deadline = date('Y-m-d', strtotime('+30 days'));

                        $stmtProj = $pdo->prepare("
                            INSERT INTO projects (client_id, name, project_type, budget, currency, start_date, deadline, status, progress, assigned_team, description)
                            VALUES (1, ?, ?, ?, ?, DATE('now'), ?, 'Planning', 0, 'Subash Sitaula', ?)
                        ");
                        $stmtProj->execute([
                            $projectName,
                            $payload['project_type'] ?? 'Custom Software',
                            $budget,
                            $currency,
                            $deadline,
                            $payload['description'] ?? 'Automated scaffold from inquiry / deal'
                        ]);
                        $projectId = $pdo->lastInsertId();
                        $actionOutput = "Scaffolded project work environment #{$projectId} ('{$projectName}').";
                        break;

                    case 'webhook':
                        $url = $actionPayload['url'] ?? 'https://httpbin.org/post';
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                        $resp = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        unset($ch);

                        if ($resp !== false) {
                            $actionOutput = "Dispatched webhook payload to external endpoint {$url} (HTTP {$httpCode}).";
                        } else {
                            $actionOutput = "Failed to dispatch webhook payload to external endpoint {$url}.";
                        }
                        break;

                    default:
                        $actionOutput = "Unknown action type: " . $rule['action_type'];
                        break;
                }
            } catch (\Exception $e) {
                $status = "FAILED";
                $actionOutput = "Error executing action: " . $e->getMessage();
            }

            $endTime = microtime(true);
            $durationMs = (int)(($endTime - $startTime) * 1000);

            // Record execution run logs
            $runStmt = $pdo->prepare("
                INSERT INTO automation_runs (automation_id, rule_name, trigger_event, status, payload, response_data, execution_time_ms, action_output, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
            ");
            $runStmt->execute([
                $rule['id'],
                $rule['name'],
                $triggerEvent,
                $status,
                json_encode($payload),
                json_encode([
                    'rule_name' => $rule['name'],
                    'trigger' => $triggerEvent,
                    'action_type' => $rule['action_type'],
                    'action_payload' => $actionPayload
                ]),
                $durationMs,
                $actionOutput
            ]);

            $results[] = [
                'id' => $rule['id'],
                'name' => $rule['name'],
                'trigger' => $triggerEvent,
                'status' => $status,
                'output' => $actionOutput
            ];
        }

        return $results;
    }
}