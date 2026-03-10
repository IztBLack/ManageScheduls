<?php
class Subject
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSubjectById($id)
    {
        $this->db->query('SELECT * FROM subjects WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAllSubjects()
    {
        $this->db->query('SELECT * FROM subjects ORDER BY subject_name ASC');
        return $this->db->resultSet();
    }

    public function addSubject($subject_name, $teacher_id = null)
    {
        try {
            $this->db->query('INSERT INTO subjects (subject_name) VALUES (:subject_name)');
            $this->db->bind(':subject_name', $subject_name);

            if ($this->db->execute()) {
                $subject_id = $this->db->lastInsertId();
                if ($teacher_id !== null) {
                    $this->db->query('INSERT INTO classes (subject_id, teacher_id) VALUES (:subject_id, :teacher_id)');
                    $this->db->bind(':subject_id', $subject_id);
                    $this->db->bind(':teacher_id', $teacher_id);
                    $this->db->execute();
                }
                return $subject_id;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die(json_encode(['success' => false, 'error' => 'Error SQL: ' . $e->getMessage()]));
        }
    }

    public function deleteSubject($id){
        $this->db->query('DELETE FROM subjects WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
