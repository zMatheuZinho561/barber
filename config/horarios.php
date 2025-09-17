<?php
// Configurações de horário de funcionamento
class HorariosConfig {
    
    // Horários de funcionamento por dia da semana
    public static function getHorariosFuncionamento() {
        return [
            'monday'    => ['inicio' => '08:00', 'fim' => '18:00'],  // Segunda
            'tuesday'   => ['inicio' => '08:00', 'fim' => '18:00'],  // Terça
            'wednesday' => ['inicio' => '08:00', 'fim' => '18:00'],  // Quarta
            'thursday'  => ['inicio' => '08:00', 'fim' => '18:00'],  // Quinta
            'friday'    => ['inicio' => '08:00', 'fim' => '18:00'],  // Sexta
            'saturday'  => ['inicio' => '08:00', 'fim' => '17:00'],  // Sábado (até 17h)
            'sunday'    => null  // Fechado aos domingos
        ];
    }
    
    // Intervalo entre agendamentos (em minutos)
    public static function getIntervaloAgendamentos() {
        return 30;
    }
    
    // Horário de almoço (opcional)
    public static function getHorarioAlmoco() {
        return [
            'inicio' => '12:00',
            'fim' => '13:00'
        ];
    }
    
    // Dias de funcionamento (para exibir ao cliente)
    public static function getDiasFuncionamento() {
        return [
            'monday'    => 'Segunda-feira',
            'tuesday'   => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday'  => 'Quinta-feira',
            'friday'    => 'Sexta-feira',
            'saturday'  => 'Sábado'
        ];
    }
    
    // Verificar se um dia está aberto
    public static function isDiaAberto($dayOfWeek) {
        $horarios = self::getHorariosFuncionamento();
        $dayNames = [
            0 => 'sunday',
            1 => 'monday', 
            2 => 'tuesday', 
            3 => 'wednesday', 
            4 => 'thursday', 
            5 => 'friday', 
            6 => 'saturday'
        ];
        
        $dayName = $dayNames[$dayOfWeek] ?? null;
        return $dayName && isset($horarios[$dayName]) && $horarios[$dayName] !== null;
    }
    
    // Obter horários para um dia específico
    public static function getHorariosDia($dayOfWeek) {
        if (!self::isDiaAberto($dayOfWeek)) {
            return null;
        }
        
        $horarios = self::getHorariosFuncionamento();
        $dayNames = [
            0 => 'sunday',
            1 => 'monday', 
            2 => 'tuesday', 
            3 => 'wednesday', 
            4 => 'thursday', 
            5 => 'friday', 
            6 => 'saturday'
        ];
        
        $dayName = $dayNames[$dayOfWeek];
        return $horarios[$dayName];
    }
}
?>