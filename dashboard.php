<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Contact.php';
require_once 'models/User.php';

// Require login
requireLogin();

$database = new Database();
$db = $database->getConnection();
$contact = new Contact($db);
$user = new User($db);

$current_user_id = getCurrentUserId();
$search = $_GET['search'] ?? '';

// Get user info
$user->getUserById($current_user_id);

// Get contacts
$contacts = $contact->readAll($current_user_id, $search);
$contact_count = $contact->getContactCount($current_user_id);

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    if ($contact->delete($delete_id, $current_user_id)) {
        header("Location: dashboard.php?message=deleted");
        exit();
    }
}

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Contact Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .contact-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .contact-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .search-box {
            border-radius: 25px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .contact-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
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
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user-cog me-2"></i>Profile
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
        <!-- Messages -->
        <?php if ($message == 'created'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Contact created successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($message == 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Contact updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($message == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Contact deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3 mb-0">My Contacts</h1>
                <p class="text-muted">Manage your personal contacts</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="add_contact.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Contact
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-address-book fa-2x mb-2"></i>
                        <h3 class="mb-0"><?php echo $contact_count; ?></h3>
                        <p class="mb-0">Total Contacts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control search-box" name="search" 
                               placeholder="Search contacts by name, email, or phone..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if ($search): ?>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contacts -->
        <?php if (empty($contacts)): ?>
            <div class="empty-state">
                <i class="fas fa-address-book fa-4x mb-3"></i>
                <h4><?php echo $search ? 'No contacts found' : 'No contacts yet'; ?></h4>
                <p class="mb-3">
                    <?php echo $search ? 'Try adjusting your search terms.' : 'Start building your contact list by adding your first contact.'; ?>
                </p>
                <?php if (!$search): ?>
                    <a href="add_contact.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Your First Contact
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($contacts as $contact_item): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card contact-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="contact-avatar me-3">
                                        <?php echo strtoupper(substr($contact_item['name'], 0, 1)); ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($contact_item['name']); ?></h5>
                                        <small class="text-muted">
                                            Added <?php echo date('M j, Y', strtotime($contact_item['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                
                                <?php if ($contact_item['email']): ?>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        <a href="mailto:<?php echo htmlspecialchars($contact_item['email']); ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($contact_item['email']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($contact_item['phone']): ?>
                                    <p class="card-text mb-2">
                                        <i class="fas fa-phone text-muted me-2"></i>
                                        <a href="tel:<?php echo htmlspecialchars($contact_item['phone']); ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($contact_item['phone']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($contact_item['notes']): ?>
                                    <p class="card-text mb-3">
                                        <i class="fas fa-sticky-note text-muted me-2"></i>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($contact_item['notes'], 0, 50)); ?>
                                            <?php if (strlen($contact_item['notes']) > 50) echo '...'; ?>
                                        </small>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="edit_contact.php?id=<?php echo $contact_item['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <a href="view_contact.php?id=<?php echo $contact_item['id']; ?>" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="dashboard.php?delete=<?php echo $contact_item['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this contact?')">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Copyright Footer -->
    <div class="text-center my-3">
        <div>
            <span class="text-secondary">© 2025 . </span>
            <span class="text-secondary">Developed by </span>
            <a href="https://rivertheme.com" class="fw-semibold text-decoration-none text-secondary fw-bold" target="_blank" rel="noopener">RiverTheme</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

