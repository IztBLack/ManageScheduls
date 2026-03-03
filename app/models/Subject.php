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
        $this->db->query('SELECT * FROM subjects');
        return $this->db->resultSet();
    }

    public function addSubject($name, $subject, $teachers)
    {
        $this->db->beginTransaction();

        $this->db->query('INSERT INTO subjects (name, subject_name) VALUES (:name, :subject_name)');
        $this->db->bind(':name', $name);
        $this->db->bind(':subject_name', $subject);

        if (!$this->db->execute()) {
            $this->db->rollBack();
            return false;
        }

        $subjectId = $this->db->lastInsertId();

        // Insertar los profesores en la tabla classes
        $this->db->query('INSERT INTO classes (subject_id, teacher_id) VALUES (:subjectId, :teacherId)');

        foreach ($teachers as $teacherId) {
            $this->db->bind(':subjectId', $subjectId);
            $this->db->bind(':teacherId', $teacherId);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return false;
            }
        }

        // Si todo ha ido bien, confirmar la transacción
        $this->db->commit();
        return true;
    }

    public function editSubject($id, $name, $subject, $teacherIds)
    {
        // Actualizar los datos básicos de la asignatura
        $this->db->query('UPDATE subjects SET name = :name, subject_name = :subject_name WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        $this->db->bind(':subject_name', $subject);
        $this->db->execute();
    
        // Verificar si hay profesores registrados para el subject actual
        $existingTeachers = $this->getAssignedTeachers($id);
    
        if (!empty($existingTeachers)) {
            // Eliminar las asignaciones existentes
            $this->removeTeachersFromSubject($id);
        }
    
        // Insertar las nuevas asignaciones de profesores
        $this->addTeachersToSubject($id, $teacherIds);
    
        return true;
    }    

    public function removeTeachersFromSubject($subjectId)
    {
        // Eliminar las asignaciones existentes para la asignatura
        $this->db->query('DELETE FROM classes WHERE subject_id = :subjectId');
        $this->db->bind(':subjectId', $subjectId);
        $this->db->execute();
    }
    
    public function addTeachersToSubject($subjectId, $teacherIds)
    {
        // Insertar las nuevas asignaciones de profesores
        $this->db->query('INSERT INTO classes (subject_id, teacher_id) VALUES (:subjectId, :teacherId)');
    
        foreach ($teacherIds as $teacherId) {
            $this->db->bind(':subjectId', $subjectId);
            $this->db->bind(':teacherId', $teacherId);
            $this->db->execute();
        }
    }

    public function getAssignedTeachers($subjectId)
    {
        $this->db->query('SELECT teacher_id FROM classes WHERE subject_id = :subject_id');
        $this->db->bind(':subject_id', $subjectId);
        return $this->db->resultSet();
    }

    public function getSchedulesWithNames(){
        $this->db->query("SELECT s.id, s.grupo, s.turno, s.aula as salon, m.name as subject_name 
                        FROM schedules s
                        INNER JOIN subjects m ON s.subject_id = m.id");
        return $this->db->resultset();
    }
    public function deleteSubject($id){
        $this->db->query('DELETE FROM subjects WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    public function filterSubjects($filter){
        $this->db->query('SELECT * FROM subjects WHERE 
            name LIKE :filter OR
            subject_name LIKE :filter');

        $this->db->bind(':filter', "%$filter%");

        return $this->db->resultSet();
    }
}
