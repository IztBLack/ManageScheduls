<?php
  class Student {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function getMyGrades($user_id){
        $this->db->query('
            SELECT s.*, sub.subject_name as subject_name, u.name as teacher_name, i.id as inscripcion_id
            FROM inscripciones i
            JOIN schedules s ON i.schedule_id = s.id
            JOIN subjects sub ON s.subject_id = sub.id
            JOIN users u ON s.teacher_id = u.id
            WHERE i.user_id = :uid
        ');
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    // Inscribir alumno (Importación masiva)
    public function registerInGroup($schedule_id, $user_id){
      $this->db->query('INSERT IGNORE INTO inscripciones (schedule_id, user_id) VALUES(:sid, :uid)');
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
public function getBonus($inscripcion_id, $unidad_id){
    $this->db->query('
        SELECT puntos 
        FROM bonus 
        WHERE inscripcion_id = :iid 
        AND unidad_id = :uid
    ');
    $this->db->bind(':iid', $inscripcion_id);
    $this->db->bind(':uid', $unidad_id);
    return $this->db->single();
}

    // Guardar una calificación individual
public function saveGrade($inscripcion_id, $actividad_id, $nota)
{
    $this->db->query('
        INSERT INTO resultados (inscripcion_id, actividad_id, calificacion)
        VALUES (:iid, :aid, :nota)
        ON DUPLICATE KEY UPDATE
        calificacion = VALUES(calificacion)
    ');

    $this->db->bind(':iid', $inscripcion_id);
    $this->db->bind(':aid', $actividad_id);
    $this->db->bind(':nota', $nota);

    return $this->db->execute();
}

/**
 * Obtener todos los bonus de un schedule, organizados por inscripción y unidad
 */
public function getBonusBySchedule($scheduleId)
{
    $this->db->query('
        SELECT 
            i.id AS inscripcion_id,
            b.unidad_id,
            b.puntos
        FROM 
            inscripciones i
        LEFT JOIN 
            bonus b ON i.id = b.inscripcion_id
        WHERE 
            i.schedule_id = :scheduleId
    ');
    
    $this->db->bind(':scheduleId', $scheduleId);
    $results = $this->db->resultSet();
    
    $bonus = [];
    foreach ($results as $row) {
        $inscripcion_id = $row->inscripcion_id;
        $unidad_id = $row->unidad_id;
        
        if (!isset($bonus[$inscripcion_id])) {
            $bonus[$inscripcion_id] = [];
        }
        
        if ($unidad_id) {
            $bonus[$inscripcion_id][$unidad_id] = $row->puntos;
        }
    }
    
    return $bonus;
}

/**
 * Guardar o actualizar bonus de un estudiante en una unidad específica
 */
public function saveBonus($inscripcion_id, $unidad_id, $puntos)
{
    // Verificar si ya existe un registro
    $this->db->query('SELECT id FROM bonus WHERE inscripcion_id = :inscripcion_id AND unidad_id = :unidad_id');
    $this->db->bind(':inscripcion_id', $inscripcion_id);
    $this->db->bind(':unidad_id', $unidad_id);
    $this->db->execute();
    
    if ($this->db->rowCount() > 0) {
        // Actualizar
        $this->db->query('UPDATE bonus SET puntos = :puntos WHERE inscripcion_id = :inscripcion_id AND unidad_id = :unidad_id');
    } else {
        // Insertar
        $this->db->query('INSERT INTO bonus (inscripcion_id, unidad_id, puntos) VALUES (:inscripcion_id, :unidad_id, :puntos)');
    }
    
    $this->db->bind(':inscripcion_id', $inscripcion_id);
    $this->db->bind(':unidad_id', $unidad_id);
    $this->db->bind(':puntos', $puntos);
    
    return $this->db->execute();
}
/**
 * Obtener estudiantes de un grupo (sin filtro por unidad)
 */
public function getStudentsInGroup($scheduleId)
{
    $this->db->query('SELECT u.id AS user_id, u.name, u.email, i.id AS inscripcion_id
                      FROM inscripciones i
                      JOIN users u ON i.user_id = u.id
                      WHERE i.schedule_id = :schedule_id');
    $this->db->bind(':schedule_id', $scheduleId);
    return $this->db->resultSet();
}
    // En models/Student.php
    public function getInscripcion($user_id, $schedule_id){
        $this->db->query('SELECT id FROM inscripciones WHERE user_id = :uid AND schedule_id = :sid');
        $this->db->bind(':uid', $user_id);
        $this->db->bind(':sid', $schedule_id);
        return $this->db->single();
    }

    public function getGradesBySchedule($schedule_id)
{
    $this->db->query('
        SELECT r.inscripcion_id, r.actividad_id, r.calificacion
        FROM resultados r
        JOIN inscripciones i ON i.id = r.inscripcion_id
        WHERE i.schedule_id = :sid
    ');

    $this->db->bind(':sid', $schedule_id);

    $resultados = $this->db->resultSet();

    $grades = [];

    foreach($resultados as $r){
        $grades[$r->inscripcion_id][$r->actividad_id] = $r->calificacion;
    }

    return $grades;
}

/**
 * Agregar estudiante al grupo (creando usuario si no existe)
 */
public function addStudentToGroup($data)
{
    // Verificar si el usuario ya existe por email
    $this->db->query('SELECT id FROM users WHERE email = :email');
    $this->db->bind(':email', $data['email']);
    $existingUser = $this->db->single();
    
    if ($existingUser) {
        $userId = $existingUser->id;
    } else {
        // Crear nuevo usuario
        $this->db->query('INSERT INTO users (name, email, password, rol) VALUES (:name, :email, :password, :rol)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':rol', $data['rol']);
        
        if (!$this->db->execute()) {
            return false;
        }
        
        $userId = $this->db->lastInsertId();
    }
    
    // Verificar si ya está inscrito
    $this->db->query('SELECT id FROM inscripciones WHERE schedule_id = :schedule_id AND user_id = :user_id');
    $this->db->bind(':schedule_id', $data['schedule_id']);
    $this->db->bind(':user_id', $userId);
    $existingInscripcion = $this->db->single();
    
    if ($existingInscripcion) {
        return 'already_exists';
    }
    
    // Inscribir
    $this->db->query('INSERT INTO inscripciones (schedule_id, user_id) VALUES (:schedule_id, :user_id)');
    $this->db->bind(':schedule_id', $data['schedule_id']);
    $this->db->bind(':user_id', $userId);
    
    return $this->db->execute() ? true : false;
}

/**
 * Eliminar estudiante del grupo
 */
public function removeFromGroup($inscripcionId)
{
    $this->db->query('DELETE FROM inscripciones WHERE id = :id');
    $this->db->bind(':id', $inscripcionId);
    return $this->db->execute();
}
  }