<?php
// ============================================================
//  seed.php — Clears all data and seeds 50+ rows per table
//  Place this file at your project root and run once in browser
// ============================================================

$host = '127.0.0.1';
$db   = 'trello_final';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<pre style="color:red">Connection failed: ' . $e->getMessage() . '</pre>');
}

$log = [];

function run(PDO $pdo, string $sql, array $params = []): void {
    $pdo->prepare($sql)->execute($params);
}

function logSection(string $title, int $count): void {
    global $log;
    $log[] = ['table' => $title, 'rows' => $count];
}

// ── Disable FK checks so we can truncate in any order ──────────────────────
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['notifications','task_submissions','tasks','project_members','projects','users'] as $t) {
    $pdo->exec("TRUNCATE TABLE `$t`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// ══════════════════════════════════════════════════════════════
//  1. USERS  (50 rows: 2 admin, 8 manager, 40 user)
// ══════════════════════════════════════════════════════════════
$password = password_hash('password', PASSWORD_BCRYPT);

$firstNames = ['Alice','Bob','Charlie','Diana','Ethan','Fiona','George','Hannah','Ivan','Julia',
               'Kevin','Laura','Mike','Nina','Oscar','Paula','Quinn','Rachel','Sam','Tina',
               'Uma','Victor','Wendy','Xander','Yara','Zoe','Aaron','Bella','Carl','Dana',
               'Eli','Faith','Greg','Holly','Ian','Jade','Karl','Lily','Mark','Nora',
               'Owen','Petra','Roy','Sara','Tom','Una','Val','Will','Xena','Yuri'];

$lastNames  = ['Smith','Johnson','Williams','Brown','Jones','Garcia','Miller','Davis','Wilson','Taylor',
               'Anderson','Thomas','Jackson','White','Harris','Martin','Thompson','Martinez','Robinson','Clark',
               'Lewis','Lee','Walker','Hall','Allen','Young','Hernandez','King','Wright','Lopez',
               'Hill','Scott','Green','Adams','Baker','Nelson','Carter','Mitchell','Perez','Roberts',
               'Turner','Phillips','Campbell','Parker','Evans','Edwards','Collins','Stewart','Morris','Rogers'];

$userIds = [];
$roles   = array_merge(['admin','admin'], array_fill(0, 8, 'manager'), array_fill(0, 40, 'user'));

for ($i = 0; $i < 50; $i++) {
    $fn    = $firstNames[$i];
    $ln    = $lastNames[$i];
    $uname = strtolower($fn . '.' . $ln);
    $email = strtolower($fn . '.' . $ln . '@example.com');
    $role  = $roles[$i];
    $date  = date('Y-m-d H:i:s', strtotime("-" . rand(10, 400) . " days"));

    run($pdo,
        "INSERT INTO users (username, email, password, role, created_at) VALUES (?,?,?,?,?)",
        [$uname, $email, $password, $role, $date]
    );
    $userIds[] = (int)$pdo->lastInsertId();
}
logSection('users', 50);

// Handy slices
$adminIds   = array_slice($userIds, 0, 2);
$managerIds = array_slice($userIds, 2, 8);
$memberIds  = array_slice($userIds, 10);   // 40 regular users

// ══════════════════════════════════════════════════════════════
//  2. PROJECTS  (50 rows)
// ══════════════════════════════════════════════════════════════
$projectTitles = [
    'E-Commerce Platform','Mobile Banking App','HR Management System','CRM Dashboard',
    'Inventory Tracker','Healthcare Portal','Learning Management System','Real Estate Marketplace',
    'Food Delivery App','Travel Booking Platform','Fleet Management System','Insurance Portal',
    'Project Management Tool','Social Media Analytics','Event Management App',
    'Supply Chain Dashboard','Legal Case Tracker','Remote Work Platform',
    'Customer Support Suite','Financial Reporting Tool','IoT Device Manager',
    'Recruitment Platform','Asset Management System','Hotel Booking Engine',
    'Sports Analytics App','Music Streaming Service','Online Auction Platform',
    'Telemedicine App','Smart Home Dashboard','Document Management System',
    'Logistics Tracker','Feedback & Survey Tool','Code Review Platform',
    'Billing & Invoicing App','Knowledge Base System','Parking Management App',
    'Restaurant POS System','Subscription Management','Campus Portal','Warehouse Management',
    'Energy Monitoring Dashboard','Voting & Poll App','Digital Signature Platform',
    'Content Management System','Multi-Tenant SaaS Shell','Compliance Tracker',
    'API Gateway Dashboard','Chatbot Builder','Data Pipeline Monitor','AI Model Registry'
];

$projectDescs = [
    'Full stack solution with modern UI and robust backend.',
    'Secure and scalable platform for enterprise use.',
    'Internal tool to streamline daily operations.',
    'Analytics and reporting for key business metrics.',
    'Real-time tracking with notifications and audit logs.',
    'User-friendly portal with role-based access control.',
    'Integrated workflows and automated notifications.',
    'Multi-tenant architecture with subscription billing.',
    'Mobile-first design with offline support.',
    'API-driven platform with third-party integrations.',
];

$projectIds = [];
for ($i = 0; $i < 50; $i++) {
    $date = date('Y-m-d H:i:s', strtotime("-" . rand(30, 365) . " days"));
    run($pdo,
        "INSERT INTO projects (title, description, created_at) VALUES (?,?,?)",
        [$projectTitles[$i], $projectDescs[$i % 10], $date]
    );
    $projectIds[] = (int)$pdo->lastInsertId();
}
logSection('projects', 50);

// ══════════════════════════════════════════════════════════════
//  3. PROJECT_MEMBERS  (50+ rows — each project gets members)
// ══════════════════════════════════════════════════════════════
$pmInserted = 0;
$seenPM = [];

foreach ($projectIds as $idx => $pid) {
    // 1 manager as admin
    $mgr = $managerIds[$idx % count($managerIds)];
    $key = "$pid-$mgr";
    if (!isset($seenPM[$key])) {
        $seenPM[$key] = true;
        $date = date('Y-m-d H:i:s', strtotime("-" . rand(1, 30) . " days"));
        run($pdo, "INSERT INTO project_members (project_id, user_id, role, joined_at) VALUES (?,?,'admin',?)", [$pid, $mgr, $date]);
        $pmInserted++;
    }
    // 2-3 members per project
    $picks = array_slice($memberIds, ($idx * 3) % count($memberIds), 3);
    foreach ($picks as $uid) {
        $key = "$pid-$uid";
        if (!isset($seenPM[$key])) {
            $seenPM[$key] = true;
            $date = date('Y-m-d H:i:s', strtotime("-" . rand(1, 25) . " days"));
            run($pdo, "INSERT INTO project_members (project_id, user_id, role, joined_at) VALUES (?,?,'member',?)", [$pid, $uid, $date]);
            $pmInserted++;
        }
    }
}
logSection('project_members', $pmInserted);

// ══════════════════════════════════════════════════════════════
//  4. TASKS  (50 rows — spread across projects)
// ══════════════════════════════════════════════════════════════
$taskTitles = [
    'Design Homepage UI','Build Product Listing','Setup Payment Gateway','Write Unit Tests',
    'Auth Module','Transfer Flow UI','Push Notifications','Leave Request Form',
    'Payroll Calculator','Employee Dashboard','API Rate Limiting','Database Optimization',
    'Setup CI/CD Pipeline','Implement Search Feature','Build Notification Service',
    'OAuth2 Integration','PDF Report Generator','CSV Data Import','Role & Permission System',
    'Multi-language Support','Dark Mode Toggle','Email Template Builder','Audit Log Module',
    'Two-Factor Authentication','Password Reset Flow','User Profile Page','File Upload Service',
    'Image Compression Module','Cache Layer Setup','WebSocket Integration',
    'Admin Panel CRUD','Data Export Tool','Analytics Dashboard','Onboarding Flow',
    'Subscription Billing','Refund Management','Feedback Widget','Live Chat Module',
    'API Documentation','Automated Backup System','Load Balancer Config','SSL Certificate Setup',
    'Error Monitoring Integration','Performance Profiling','A/B Testing Framework',
    'SEO Meta Tags Module','Social Login Integration','Webhook Manager','Queue Worker Setup',
    'Containerize with Docker'
];

$taskDescs = [
    'Create wireframes and mockups for the main interface.',
    'Implement grid layout with filters and pagination.',
    'Integrate third-party payment provider for checkout.',
    'Cover core logic with unit and integration tests.',
    'JWT login, refresh tokens and session management.',
    'Design and build the primary user-facing screens.',
    'Firebase/FCM integration for real-time alerts.',
    'Build form with approval workflow and notifications.',
    'Implement computation with tax and deduction rules.',
    'Overview page showing stats and key metrics.',
];

$statuses    = ['open','in_progress','submitted','done'];
$statusWeights = [15, 15, 10, 10]; // rough distribution, cycle through

$taskIds = [];
for ($i = 0; $i < 50; $i++) {
    $pid    = $projectIds[$i % count($projectIds)];

    // Pick a member from this project
    $pmForProject = array_filter(array_keys($seenPM), fn($k) => str_starts_with($k, "$pid-"));
    $assignee = null;
    if (!empty($pmForProject)) {
        $randomKey = array_values($pmForProject)[array_rand($pmForProject)];
        $assignee  = (int)explode('-', $randomKey)[1];
    }

    $status     = $statuses[$i % 4];
    $complexity = rand(1, 9);
    $date       = date('Y-m-d H:i:s', strtotime("-" . rand(1, 60) . " days"));

    run($pdo,
        "INSERT INTO tasks (title, description, status, complexity, project_id, assigned_to, created_at) VALUES (?,?,?,?,?,?,?)",
        [$taskTitles[$i], $taskDescs[$i % 10], $status, $complexity, $pid, $assignee, $date]
    );
    $taskIds[] = (int)$pdo->lastInsertId();
}
logSection('tasks', 50);

// ══════════════════════════════════════════════════════════════
//  5. TASK_SUBMISSIONS  (50 rows — for submitted/done tasks)
// ══════════════════════════════════════════════════════════════
$gitRepos = ['alice','bob','charlie','diana','ethan','fiona','george','hannah'];
$subMessages = [
    'All acceptance criteria met. Ready for review.',
    'Feature complete. Unit tests passing at 92%.',
    'Implementation done. Please review the PR.',
    'Fixed all review comments. Resubmitting.',
    'Done with initial implementation and documentation.',
    'Refactored as requested. Tests updated.',
    'Integrated with existing modules. No breaking changes.',
    'Performance optimized. Benchmark results attached.',
    'Edge cases handled. Code reviewed internally.',
    'Completed ahead of schedule. CI passing.',
];
$subStatuses = ['pending','approved','rejected'];

$subCount = 0;
// Use tasks that are submitted or done first, then pad with others
$eligibleTasks = array_filter($taskIds, fn($id, $i) => in_array($statuses[$i % 4], ['submitted','done']), ARRAY_FILTER_USE_BOTH);
$eligibleTasks = array_values($eligibleTasks);
$remainingTasks = array_values(array_diff($taskIds, $eligibleTasks));
$orderedTasks = array_merge($eligibleTasks, $remainingTasks);

$usedTaskSub = [];
for ($i = 0; $i < 50; $i++) {
    $tid   = $orderedTasks[$i % count($orderedTasks)];
    $repo  = $gitRepos[$i % count($gitRepos)];
    $prNum = rand(10, 200);
    $link  = "https://github.com/$repo/project/pull/$prNum";
    $msg   = $subMessages[$i % 10];
    $stat  = $subStatuses[$i % 3];
    $date  = date('Y-m-d H:i:s', strtotime("-" . rand(1, 30) . " days"));

    run($pdo,
        "INSERT INTO task_submissions (task_id, git_link, message, status, created_at) VALUES (?,?,?,?,?)",
        [$tid, $link, $msg, $stat, $date]
    );
    $subCount++;
}
logSection('task_submissions', $subCount);

// ══════════════════════════════════════════════════════════════
//  6. NOTIFICATIONS  (50 rows)
// ══════════════════════════════════════════════════════════════
$notifTypes = ['approved','rejected'];
for ($i = 0; $i < 50; $i++) {
    $uid   = $memberIds[$i % count($memberIds)];
    $tid   = $taskIds[$i % count($taskIds)];
    $type  = $notifTypes[$i % 2];
    $tname = $taskTitles[$i % count($taskTitles)];
    $msg   = $type === 'approved'
        ? "✅ Your submission for task \"$tname\" has been approved!"
        : "❌ Your submission for task \"$tname\" was rejected. Please review and resubmit.";
    $read  = rand(0, 1);
    $date  = date('Y-m-d H:i:s', strtotime("-" . rand(1, 20) . " days"));

    run($pdo,
        "INSERT INTO notifications (user_id, task_id, type, message, is_read, created_at) VALUES (?,?,?,?,?,?)",
        [$uid, $tid, $type, $msg, $read, $date]
    );
}
logSection('notifications', 50);

// ══════════════════════════════════════════════════════════════
//  OUTPUT
// ══════════════════════════════════════════════════════════════
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Seeder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#f4f7fc; font-family:Arial,sans-serif; }
        .hero { background:linear-gradient(135deg,#667eea,#764ba2); border-radius:20px; color:white; }
        .soft-card { border:none; border-radius:20px; box-shadow:0 10px 30px rgba(15,23,42,.06); }
        .stat-icon { width:55px;height:55px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:22px; }
    </style>
</head>
<body>
<div class="container py-5">

    <div class="hero p-4 mb-4 shadow-sm">
        <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2)">
            <i class="fas fa-database me-1"></i> Seeder
        </span>
        <h2 class="fw-bold mb-1">Database Seeded Successfully</h2>
        <p class="mb-0 text-white-50">All tables cleared and filled with fresh data.</p>
    </div>

    <div class="row g-4 mb-4">
        <?php
        $icons  = ['users'=>'users','projects'=>'project-diagram','project_members'=>'user-friends',
                   'tasks'=>'tasks','task_submissions'=>'file-upload','notifications'=>'bell'];
        $colors = ['users'=>'text-primary','projects'=>'text-success','project_members'=>'text-info',
                   'tasks'=>'text-warning','task_submissions'=>'text-danger','notifications'=>'text-secondary'];
        foreach ($log as $entry):
            $t = $entry['table']; $icon = $icons[$t] ?? 'table'; $color = $colors[$t] ?? 'text-primary';
        ?>
        <div class="col-md-6 col-xl-4">
            <div class="card soft-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon <?= $color ?>" style="background:rgba(102,126,234,.08)">
                        <i class="fas fa-<?= $icon ?>"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size:.75rem;letter-spacing:.05em"><?= $t ?></div>
                        <div class="h3 fw-bold mb-0"><?= $entry['rows'] ?> <small class="fs-6 text-muted fw-normal">rows</small></div>
                    </div>
                    <div class="ms-auto">
                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card soft-card">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-key text-warning me-2"></i>Login Credentials</h6>
            <p class="text-muted mb-3">All seeded users share the same password:</p>
            <div class="d-flex gap-3 flex-wrap">
                <div class="p-3 rounded-3" style="background:#f8faff;border:1px solid #e2e8f0">
                    <small class="text-muted d-block mb-1">Password (all users)</small>
                    <code class="fw-bold fs-5">password123</code>
                </div>
                <div class="p-3 rounded-3" style="background:#f8faff;border:1px solid #e2e8f0">
                    <small class="text-muted d-block mb-1">Admin example</small>
                    <code>alice.smith@example.com</code>
                </div>
                <div class="p-3 rounded-3" style="background:#f8faff;border:1px solid #e2e8f0">
                    <small class="text-muted d-block mb-1">Manager example</small>
                    <code>charlie.williams@example.com</code>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>