<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Contact.php';

// Require login
requireLogin();

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$contact = new Contact($db);

$current_user_id = getCurrentUserId();

// Get user info
$user->getUserById($current_user_id);

// Get user statistics
$contact_count = $contact->getContactCount($current_user_id);

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill in all password fields.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'New password must be at least 6 characters long.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } else {
        // Verify current password
        if ($user->login($user->username, $current_password)) {
            // Update password
            $query = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
            $stmt = $db->prepare($query);
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password_hash", $new_password_hash);
            $stmt->bindParam(":id", $current_user_id);
            
            if ($stmt->execute()) {
                $success_message = 'Password updated successfully!';
            } else {
                $error_message = 'Failed to update password. Please try again.';
            }
        } else {
            $error_message = 'Current password is incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .profile-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2.5rem;
            margin: 0 auto 1rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        .profile-body {
            padding: 2rem;
        }
        .stats-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-address-book me-2"></i>Contact Manager
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user->username); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="profile-container">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($user->username, 0, 1)); ?>
                        </div>
                        <h2 class="mb-2"><?php echo htmlspecialchars($user->username); ?></h2>
                        <p class="mb-0 opacity-75"><?php echo htmlspecialchars($user->email); ?></p>
                        <small class="opacity-75">
                            <i class="fas fa-calendar me-1"></i>
                            Member since <?php echo date('F Y', strtotime($user->created_at)); ?>
                        </small>
                    </div>

                    <!-- Profile Body -->
                    <div class="profile-body">
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <i class="fas fa-address-book fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1"><?php echo $contact_count; ?></h4>
                                    <p class="mb-0 text-muted">Total Contacts</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stats-card">
                                    <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1"><?php echo ceil((time() - strtotime($user->created_at)) / (60 * 60 * 24)); ?></h4>
                                    <p class="mb-0 text-muted">Days Active</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog me-2"></i>Account Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4"><strong>Username:</strong></div>
                                    <div class="col-sm-8"><?php echo htmlspecialchars($user->username); ?></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-4"><strong>Email:</strong></div>
                                    <div class="col-sm-8"><?php echo htmlspecialchars($user->email); ?></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-4"><strong>Member Since:</strong></div>
                                    <div class="col-sm-8"><?php echo date('F j, Y', strtotime($user->created_at)); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($error_message): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($success_message): ?>
                                    <div class="alert alert-success" role="alert">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <?php echo htmlspecialchars($success_message); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" 
                                               name="new_password" minlength="6" required>
                                        <div class="form-text">At least 6 characters</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" 
                                               name="confirm_password" required>
                                        <div id="passwordMatch" class="form-text"></div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-outline-primary me-2">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                            <a href="add_contact.php" class="btn btn-outline-success">
                                <i class="fas fa-plus me-2"></i>Add Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword === '') {
                matchDiv.textContent = '';
                matchDiv.className = 'form-text';
            } else if (newPassword === confirmPassword) {
                matchDiv.textContent = 'Passwords match';
                matchDiv.className = 'form-text text-success';
            } else {
                matchDiv.textContent = 'Passwords do not match';
                matchDiv.className = 'form-text text-danger';
            }
        });
    </script>
</body>
</html>

