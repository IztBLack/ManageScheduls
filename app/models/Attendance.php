<?php
class Attendance {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Obtener alumnos de un grupo específico para el pase de lista
    public function getStudentsForAttendance($schedule_id) {
        $this->db->query('SELECT i.id as inscripcion_id, u.id as user_id, u.name, u.email
                          FROM inscripciones i
                          JOIN users u ON i.user_id = u.id
                          WHERE i.schedule_id = :schedule_id
                          ORDER BY u.name ASC');
        $this->db->bind(':schedule_id', $schedule_id);
        return $this->db->resultSet();
    }

    // Obtener las asistencias registradas en una fecha concreta para un grupo
    // Devuelve un array asociativo usando inscripcion_id como llave
    public function getAttendanceByDate($schedule_id, $fecha) {
        $this->db->query('SELECT a.inscripcion_id, a.estado 
                          FROM asistencias a
                          JOIN inscripciones i ON a.inscripcion_id = i.id
                          WHERE i.schedule_id = :schedule_id AND a.fecha = :fecha');
        $this->db->bind(':schedule_id', $schedule_id);
        $this->db->bind(':fecha', $fecha);
        $results = $this->db->resultSet();
        
        $attendanceData = [];
        foreach($results as $row) {
            $attendanceData[$row->inscripcion_id] = $row->estado;
        }
        return $attendanceData;
    }

    // Guardar o actualizar la asistencia de un alumno usando ON DUPLICATE KEY UPDATE
    public function saveAttendance($inscripcion_id, $fecha, $estado) {
        $this->db->query('INSERT INTO asistencias (inscripcion_id, fecha, estado) 
                          VALUES (:inscripcion_id, :fecha, :estado)
                          ON DUPLICATE KEY UPDATE estado = :estado2');
        
        $this->db->bind(':inscripcion_id', $inscripcion_id);
        $this->db->bind(':fecha', $fecha);
        $this->db->bind(':estado', $estado);
        $this->db->bind(':estado2', $estado); // Para el update
        return $this->db->execute();
    }

    // Obtener todas las fechas únicas en las que se ha pasado lista para un grupo
    public function getAttendanceDates($schedule_id) {
        $this->db->query('SELECT DISTINCT a.fecha 
                          FROM asistencias a
                          JOIN inscripciones i ON a.inscripcion_id = i.id
                          WHERE i.schedule_id = :schedule_id
                          ORDER BY a.fecha ASC');
        $this->db->bind(':schedule_id', $schedule_id);
        
        $results = $this->db->resultSet();
        $dates = [];
        foreach($results as $row) {
            $dates[] = $row->fecha;
        }
        return $dates;
    }

    // Obtener todo el historial de asistencias de un grupo
    // Retorna array: [inscripcion_id][fecha] = estado
    public function getAllAttendanceRecords($schedule_id) {
        $this->db->query('SELECT a.inscripcion_id, a.fecha, a.estado 
                          FROM asistencias a
                          JOIN inscripciones i ON a.inscripcion_id = i.id
                          WHERE i.schedule_id = :schedule_id');
        $this->db->bind(':schedule_id', $schedule_id);
        
        $results = $this->db->resultSet();
        $records = [];
        foreach($results as $row) {
            $records[$row->inscripcion_id][$row->fecha] = $row->estado;
        }
        return $records;
    }

    // ===================================
    // Eliminar pases de lista por fecha
    // ===================================
    public function deleteRecordsByDate($schedule_id, $fecha) {
        $this->db->query('DELETE a FROM asistencias a 
                          INNER JOIN inscripciones i ON a.inscripcion_id = i.id 
                          WHERE i.schedule_id = :schedule_id AND a.fecha = :fecha');
        
        $this->db->bind(':schedule_id', $schedule_id);
        $this->db->bind(':fecha', $fecha);

        return $this->db->execute();
    }
}
?>
