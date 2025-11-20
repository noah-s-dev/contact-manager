<?php
/**
 * Contact Model
 * Handles contact CRUD operations
 */

class Contact {
    private $conn;
    private $table_name = "contacts";

    public $id;
    public $user_id;
    public $name;
    public $email;
    public $phone;
    public $notes;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new contact
     * @return bool
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, name=:name, email=:email, phone=:phone, notes=:notes";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":notes", $this->notes);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Read all contacts for a user
     * @param int $user_id
     * @param string $search
     * @return array
     */
    public function readAll($user_id, $search = '') {
        $query = "SELECT id, name, email, phone, notes, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";

        if (!empty($search)) {
            $query .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
        }

        $query .= " ORDER BY name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);

        if (!empty($search)) {
            $search_param = "%{$search}%";
            $stmt->bindParam(":search", $search_param);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Read a single contact
     * @param int $id
     * @param int $user_id
     * @return bool
     */
    public function readOne($id, $user_id) {
        $query = "SELECT id, name, email, phone, notes, created_at, updated_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->notes = $row['notes'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    /**
     * Update a contact
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, notes=:notes 
                  WHERE id=:id AND user_id=:user_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->notes = htmlspecialchars(strip_tags($this->notes));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    /**
     * Delete a contact
     * @param int $id
     * @param int $user_id
     * @return bool
     */
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    /**
     * Get contact count for a user
     * @param int $user_id
     * @return int
     */
    public function getContactCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['count'];
    }

    /**
     * Validate contact data
     * @return array
     */
    public function validate() {
        $errors = [];

        if (empty(trim($this->name))) {
            $errors[] = 'Name is required.';
        }

        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($this->email) && empty($this->phone)) {
            $errors[] = 'Either email or phone number is required.';
        }

        return $errors;
    }
}
?>

