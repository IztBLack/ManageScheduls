<?php
  class Schedule {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function addFullStructure($data) {
    try {
        $this->db->beginTransaction();

        // 1. Insertar el Grupo (Schedules)
        $this->db->query('INSERT INTO schedules (teacher_id, subject_id, grupo, turno, aula, periodo, especialidad) 
                          VALUES (:tid, :sid, :grp, :trn, :aul, :per, :esp)');
        $this->db->bind(':tid', $data['teacher_id']);
        $this->db->bind(':sid', $data['subject_id']);
        $this->db->bind(':grp', $data['grupo']);
        $this->db->bind(':trn', $data['turno']);
        $this->db->bind(':aul', $data['aula']);
        $this->db->bind(':per', $data['periodo']);
        $this->db->bind(':esp', $data['especialidad']);
        $this->db->execute();
        
        $schedule_id = $this->db->lastInsertId();

        // 2. Insertar Unidades
        if(isset($data['units'])){
            foreach($data['units'] as $u_index => $unit){
                $this->db->query('INSERT INTO unidades (schedule_id, nombre, orden) VALUES (:sid, :nom, :ord)');
                $this->db->bind(':sid', $schedule_id);
                $this->db->bind(':nom', "Unidad $u_index");
                $this->db->bind(':ord', $u_index);
                $this->db->execute();
                $unidad_id = $this->db->lastInsertId();

                // 3. Insertar Actividades de esa unidad
                foreach($unit['activities'] as $act){
                    $this->db->query('INSERT INTO actividades (unidad_id, nombre, ponderacion) VALUES (:uid, :nom, :pon)');
                    $this->db->bind(':uid', $unidad_id);
                    $this->db->bind(':nom', $act['name']);
                    $this->db->bind(':pon', $act['weight']);
                    $this->db->execute();
                }
            }
        }

        return $this->db->commit();
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log('Schedule::addFullStructure error: ' . $e->getMessage());
        return false;
    }
}

    // Obtener todos los horarios con el nombre de la materia y del grupo
    public function getSchedulesWithNames()
    {
        // select `grupo` directly; views expect `$schedule->grupo` now
        $this->db->query('SELECT s.*, su.subject_name AS subject_name, s.grupo
                          FROM schedules s
                          LEFT JOIN subjects su ON s.subject_id = su.id');
        return $this->db->resultSet();
    }

    // Recuperar un solo horario con detalles relacionados
    public function getScheduleById($id)
    {
        $this->db->query('SELECT s.*, su.subject_name AS subject_name, s.grupo
                          FROM schedules s
                          LEFT JOIN subjects su ON s.subject_id = su.id
                          WHERE s.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Actividades que pertenecen al horario (se pueden agrupar por unidad si es necesario)
    public function getActivitiesBySchedule($schedule_id)
    {
        $this->db->query('SELECT a.*, u.nombre AS unidad_nombre, u.id AS unidad_id
                          FROM actividades a
                          JOIN unidades u ON a.unidad_id = u.id
                          WHERE u.schedule_id = :sid
                          ORDER BY u.orden, a.id');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->resultSet();
    }

    /**
     * Devuelve las unidades de un horario con sus actividades incrustadas.
     * Utilizado por la vista de edición.
     */
    public function getUnitsWithActivities($schedule_id)
    {
        // obtener unidades
        $this->db->query('SELECT * FROM unidades WHERE schedule_id = :sid ORDER BY orden');
        $this->db->bind(':sid', $schedule_id);
        $units = $this->db->resultSet();

        // para cada unidad, anexar actividades
        foreach ($units as $unit) {
            $this->db->query('SELECT * FROM actividades WHERE unidad_id = :uid');
            $this->db->bind(':uid', $unit->id);
            $unit->actividades = $this->db->resultSet();
        }

        return $units;
    }

    // Lista de alumnos inscritos en el horario
    public function getStudentsBySchedule($schedule_id)
    {
        $this->db->query('SELECT u.id, u.name
                          FROM inscripciones i
                          JOIN users u ON i.user_id = u.id
                          WHERE i.schedule_id = :sid');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->resultSet();
    }

    // Obtener grupos por docente (Autogestión)
    public function getSchedulesByTeacher($teacher_id){
      $this->db->query('SELECT * FROM schedules WHERE teacher_id = :tid');
      $this->db->bind(':tid', $teacher_id);
      return $this->db->resultSet();
    }

    // Obtener todas las materias disponibles
    public function getSubjects()
    {
      $this->db->query('SELECT id, subject_name FROM subjects ORDER BY subject_name');
      return $this->db->resultSet();
    }

    
  }