<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Contact.php';

// Require login
requireLogin();

$database = new Database();
$db = $database->getConnection();
$contact = new Contact($db);

$current_user_id = getCurrentUserId();
$contact_id = $_GET['id'] ?? 0;

// Load contact
if (!$contact->readOne($contact_id, $current_user_id)) {
    header("Location: dashboard.php");
    exit();
}

$error_messages = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contact->user_id = $current_user_id;
    $contact->name = trim($_POST['name'] ?? '');
    $contact->email = trim($_POST['email'] ?? '');
    $contact->phone = trim($_POST['phone'] ?? '');
    $contact->notes = trim($_POST['notes'] ?? '');

    // Validate
    $error_messages = $contact->validate();

    if (empty($error_messages)) {
        if ($contact->update()) {
            header("Location: dashboard.php?message=updated");
            exit();
        } else {
            $error_messages[] = 'Failed to update contact. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 2rem;
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
        .contact-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
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
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="form-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-edit fa-3x text-primary mb-3"></i>
                        <h2>Edit Contact</h2>
                        <p class="text-muted">Update contact information</p>
                    </div>

                    <div class="contact-info">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Created: <?php echo date('F j, Y \a\t g:i A', strtotime($contact->created_at)); ?>
                        </small>
                        <?php if ($contact->updated_at != $contact->created_at): ?>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-edit me-1"></i>
                                Last updated: <?php echo date('F j, Y \a\t g:i A', strtotime($contact->updated_at)); ?>
                            </small>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($error_messages)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($error_messages as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? $contact->name); ?>" 
                                   placeholder="Enter full name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? $contact->email); ?>" 
                                   placeholder="Enter email address">
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-2"></i>Phone Number
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? $contact->phone); ?>" 
                                   placeholder="Enter phone number">
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Notes
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                      placeholder="Add any additional notes about this contact"><?php echo htmlspecialchars($_POST['notes'] ?? $contact->notes); ?></textarea>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>At least one contact method (email or phone) is required.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="view_contact.php?id=<?php echo $contact->id; ?>" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>View Contact
                            </a>
                            <div>
                                <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Contact
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>

