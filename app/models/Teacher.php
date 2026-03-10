<?php

class Teacher {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getTeacherByUserId($user_id) {
        $this->db->query('SELECT * FROM teachers WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function checkDuplicates($curp, $rfc, $clave) {
        // Retorna un array con los campos que ya existen en la BD
        $duplicates = [];

        if (!empty($curp)) {
            $this->db->query('SELECT id FROM teachers WHERE curp = :curp');
            $this->db->bind(':curp', $curp);
            if ($this->db->single()) $duplicates[] = 'CURP';
        }

        if (!empty($rfc)) {
            $this->db->query('SELECT id FROM teachers WHERE rfc = :rfc');
            $this->db->bind(':rfc', $rfc);
            if ($this->db->single()) $duplicates[] = 'RFC';
        }

        if (!empty($clave)) {
            $this->db->query('SELECT id FROM teachers WHERE clave = :clave');
            $this->db->bind(':clave', $clave);
            if ($this->db->single()) $duplicates[] = 'Clave de Empleado';
        }

        return $duplicates;
    }

    public function addTeacher($name, $lastName1, $lastName2, $curp, $rfc, $clave, $user_id = null) {
        $this->db->query('INSERT INTO teachers (name, lastName1, lastName2, curp, rfc, clave, user_id) VALUES (:name, :lastName1, :lastName2, :curp, :rfc, :clave, :user_id)');
        $this->db->bind(':name', $name);
        $this->db->bind(':lastName1', $lastName1);
        $this->db->bind(':lastName2', $lastName2);
        $this->db->bind(':curp', $curp);
        $this->db->bind(':rfc', $rfc);
        $this->db->bind(':clave', $clave);
        $this->db->bind(':user_id', $user_id);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function getTeacherById($id) {
        $this->db->query('SELECT * FROM teachers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAllTeachers() {
        $this->db->query('SELECT * FROM teachers');
        return $this->db->resultSet();
    }

    public function updateTeacher($id, $name, $lastName1, $lastName2, $curp, $rfc, $clave) {
        $this->db->query('UPDATE teachers SET name = :name, lastName1 = :lastName1, lastName2 = :lastName2, curp = :curp, rfc = :rfc, clave = :clave WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        $this->db->bind(':lastName1', $lastName1);
        $this->db->bind(':lastName2', $lastName2);
        $this->db->bind(':curp', $curp);
        $this->db->bind(':rfc', $rfc);
        $this->db->bind(':clave', $clave);

        return $this->db->execute();
    }
    
    public function deleteTeacher($id) {
        $this->db->query('DELETE FROM teachers WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function filterTeachers($filter) {
        $this->db->query('SELECT * FROM teachers WHERE 
            name LIKE :filter OR
            lastName1 LIKE :filter OR
            lastName2 LIKE :filter OR
            curp LIKE :filter OR
            rfc LIKE :filter OR
            clave LIKE :filter');

        $this->db->bind(':filter', "%$filter%");

        return $this->db->resultSet();
    }
}

