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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contact->name); ?> - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .contact-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .contact-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .contact-avatar {
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
        .contact-body {
            padding: 2rem;
        }
        .contact-info-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        .contact-info-item:last-child {
            margin-bottom: 0;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .action-buttons {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 1rem 2rem;
            border-top: 1px solid #dee2e6;
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
                <div class="contact-container">
                    <!-- Contact Header -->
                    <div class="contact-header">
                        <div class="contact-avatar">
                            <?php echo strtoupper(substr($contact->name, 0, 1)); ?>
                        </div>
                        <h2 class="mb-2"><?php echo htmlspecialchars($contact->name); ?></h2>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-calendar me-2"></i>
                            Added <?php echo date('F j, Y', strtotime($contact->created_at)); ?>
                        </p>
                    </div>

                    <!-- Contact Body -->
                    <div class="contact-body">
                        <!-- Email -->
                        <?php if ($contact->email): ?>
                            <div class="contact-info-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-envelope text-primary me-2"></i>Email Address
                                        </h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($contact->email); ?></p>
                                    </div>
                                    <div>
                                        <a href="mailto:<?php echo htmlspecialchars($contact->email); ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-paper-plane me-1"></i>Send Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Phone -->
                        <?php if ($contact->phone): ?>
                            <div class="contact-info-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                        </h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($contact->phone); ?></p>
                                    </div>
                                    <div>
                                        <a href="tel:<?php echo htmlspecialchars($contact->phone); ?>" 
                                           class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-phone me-1"></i>Call
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Notes -->
                        <?php if ($contact->notes): ?>
                            <div class="contact-info-item">
                                <h6 class="mb-2">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>Notes
                                </h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($contact->notes)); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Contact Details -->
                        <div class="contact-info-item">
                            <h6 class="mb-2">
                                <i class="fas fa-info-circle text-primary me-2"></i>Contact Details
                            </h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted">Created:</small><br>
                                    <small><?php echo date('F j, Y \a\t g:i A', strtotime($contact->created_at)); ?></small>
                                </div>
                                <?php if ($contact->updated_at != $contact->created_at): ?>
                                    <div class="col-sm-6">
                                        <small class="text-muted">Last Updated:</small><br>
                                        <small><?php echo date('F j, Y \a\t g:i A', strtotime($contact->updated_at)); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Empty state for missing info -->
                        <?php if (!$contact->email && !$contact->phone): ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No contact information available. Consider adding an email or phone number.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Contacts
                            </a>
                            <div>
                                <a href="edit_contact.php?id=<?php echo $contact->id; ?>" 
                                   class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-2"></i>Edit Contact
                                </a>
                                <a href="dashboard.php?delete=<?php echo $contact->id; ?>" 
                                   class="btn btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to delete this contact?')">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

