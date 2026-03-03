<?php
  class Student {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Inscribir alumno (Importación masiva)
    public function registerInGroup($schedule_id, $user_id){
      $this->db->query('INSERT INTO inscripciones (schedule_id, user_id) VALUES(:sid, :uid)');
      $this->db->bind(':sid', $schedule_id);
      $this->db->bind(':uid', $user_id);
      return $this->db->execute();
    }

    // Obtener resultados detallados para el cálculo (Fórmula PDF)
    public function getGradesForCalculation($inscripcion_id, $unidad_id){
      $this->db->query('
        SELECT r.calificacion, a.ponderacion 
        FROM resultados r
        JOIN actividades a ON r.actividad_id = a.id
        WHERE r.inscripcion_id = :iid AND a.unidad_id = :uid
      ');
      $this->db->bind(':iid', $inscripcion_id);
      $this->db->bind(':uid', $unidad_id);
      return $this->db->resultSet();
    }

    // Obtener Bonus
    public function getBonus($inscripcion_id, $referencia_id){
      $this->db->query('SELECT puntos FROM bonus WHERE inscripcion_id = :iid AND referencia_id = :rid');
      $this->db->bind(':iid', $inscripcion_id);
      $this->db->bind(':rid', $referencia_id);
      return $this->db->single();
    }

    // Guardar una calificación individual
    public function saveGrade($inscripcion_id, $actividad_id, $nota)
    {
        // intenta insertar o actualizar
        $this->db->query('REPLACE INTO resultados (inscripcion_id, actividad_id, calificacion) VALUES(:iid, :aid, :cal)');
        $this->db->bind(':iid', $inscripcion_id);
        $this->db->bind(':aid', $actividad_id);
        $this->db->bind(':cal', $nota);
        return $this->db->execute();
    }

    // Obtener alumnos inscritos por horario
    public function getStudentsInGroup($schedule_id)
    {
        $this->db->query('SELECT u.id, u.name
                          FROM inscripciones i
                          JOIN users u ON i.user_id = u.id
                          WHERE i.schedule_id = :sid');
        $this->db->bind(':sid', $schedule_id);
        return $this->db->resultSet();
    }
  }