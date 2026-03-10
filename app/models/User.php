<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Add User / Register
    public function register($data) {
        // Preparar Query
        $sql = 'INSERT INTO users (name, email, password, rol';
        $values = 'VALUES (:name, :email, :password, :rol';
        
        if (isset($data['must_change_password'])) {
            $sql .= ', must_change_password';
            $values .= ', :must_change_password';
        }
        $sql .= ') ' . $values . ')';
        
        $this->db->query($sql);

        // Bind Values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':rol', $data['rol'] ?? 'alumno');
        
        if (isset($data['must_change_password'])) {
            $this->db->bind(':must_change_password', $data['must_change_password']);
        }

        // Execute
        return $this->db->execute();
    }

    // Find User By Email
    public function findUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check Rows
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Login / Authenticate User
    public function login($email, $password) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row->password;
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }

        return false;
    }

    // Find User By ID
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Buscar usuario por email y devolver registro
    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(':email', $email);

        return $this->db->single();
    }

    // Crear un alumno simple (usa email derivado de matrícula si aplica)
    public function createStudent($name, $email, $plainPassword)
    {
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
        $this->db->query('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $this->db->bind(':name', $name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashed);
        return $this->db->execute();
    }

    public function updateStudent($data)
{
    // Actualizar nombre y email (matricula@students.local)
    $this->db->query('UPDATE users SET name = :name, email = :email WHERE id = :id');
    $this->db->bind(':name',  $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':id',    $data['user_id']);
    return $this->db->execute();
}

    // Update password and clear must_change_password flag
    public function updatePassword($id, $new_password) {
        $this->db->query('UPDATE users SET password = :password, must_change_password = 0 WHERE id = :id');
        $this->db->bind(':password', password_hash($new_password, PASSWORD_DEFAULT));
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deleteStudent($id)
    {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>