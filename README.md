# 📞 Contact Manager

A modern, responsive contact management system built with PHP and MySQL. Manage your contacts efficiently with a clean, user-friendly interface.

## 🛠️ Technologies Used

- **Backend**: PHP 8.2+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.1.3
- **Icons**: Font Awesome 6.0.0
- **Server**: Apache 2.4+
- **Security**: PDO with prepared statements, password hashing

## 📋 Project Overview

Contact Manager is a web-based application that allows users to store, organize, and manage their personal and professional contacts. The system provides a secure, user-friendly interface for adding, editing, viewing, and deleting contact information.

## ✨ Key Features

- **User Authentication**: Secure login and registration system
- **Contact Management**: Add, edit, view, and delete contacts
- **Search Functionality**: Find contacts quickly with real-time search
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Data Security**: Password hashing and SQL injection protection
- **User Profiles**: Manage account information and settings
- **Clean Interface**: Modern, intuitive user interface

## 👥 User Roles

### Regular User
- Register and login to the system
- Manage personal contact list
- Add, edit, view, and delete contacts
- Search through contacts
- Update profile information

### Default Demo User
- Username: `john`
- Password: `john123`
- Pre-loaded with sample contacts for demonstration

## 📁 Project Structure

```
contact_manager/
├── .htaccess                 # Apache security configuration
├── database.sql             # Database schema and sample data
├── index.php                # Main entry point
├── login.php                # User authentication
├── register.php             # User registration
├── dashboard.php            # Main dashboard
├── add_contact.php          # Add new contacts
├── edit_contact.php         # Edit existing contacts
├── view_contact.php         # View contact details
├── profile.php              # User profile management
├── logout.php               # User logout
├── config/
│   ├── database.php         # Database connection
│   └── session.php          # Session management
├── models/
│   ├── User.php             # User model
│   └── Contact.php          # Contact model
└── classes/
    └── Validator.php        # Input validation
```

## 🚀 Setup Instructions

### Prerequisites
- XAMPP, WAMP, or similar local server environment
- PHP 8.2 or higher
- MySQL 5.7 or higher
- Apache web server

### Installation Steps

1. **Clone or Download**
   ```bash
   # Download the project files to your web server directory
   # Example: C:\xampp\htdocs\contact_manager\
   ```

2. **Database Setup**
   - Open phpMyAdmin or MySQL command line
   - Import the `database.sql` file
   - The database `contact_manager` will be created automatically

3. **Configuration**
   - Edit `config/database.php` if needed:
     ```php
     private $host = 'localhost';
     private $db_name = 'contact_manager';
     private $username = 'root';
     private $password = '';
     ```

4. **Access the Application**
   - Start your web server (Apache, MySQL)
   - Navigate to: `http://localhost/contact-manager/` (or `http://localhost/contact_manager/` depending on your folder name)
   - **Note**: The application uses relative paths for all redirects, so it will work correctly with `http://localhost/project_name/` format
   - Login with demo credentials:
     - Username: `john`
     - Password: `john123`

## 📖 Usage

### Getting Started
1. **Login**: Use the demo account or register a new account
2. **Dashboard**: View all your contacts in a clean card layout
3. **Add Contacts**: Click "Add Contact" to create new entries
4. **Search**: Use the search bar to find specific contacts
5. **Manage**: Edit, view, or delete contacts as needed

### Features in Detail
- **Contact Cards**: Each contact displays name, email, phone, and notes
- **Quick Actions**: Edit, view, and delete buttons on each contact
- **Responsive Design**: Optimized for all device sizes
- **Data Validation**: Input validation for all forms
- **Security**: Secure session management and data protection

## 🎯 Intended Use

This Contact Manager is designed for:
- **Personal Use**: Manage personal contacts and relationships
- **Small Business**: Organize client and customer information
- **Educational Purposes**: Learn PHP, MySQL, and web development
- **Demo Applications**: Showcase web development skills
- **Starting Point**: Base for more complex contact management systems

## 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a Pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).
