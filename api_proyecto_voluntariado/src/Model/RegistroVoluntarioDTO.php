<?php

namespace App\Model;

class RegistroVoluntarioDTO
{
    public function __construct(
        public string $nombreCompleto, // Angular enviará "Juan Pérez"
        public string $dni,
        public string $correo,
        public string $password,       // Necesario para el login
        public ?string $zona = null,
        public ?string $ciclo = null,
        public ?string $fechaNacimiento = null, // Llegará como string "YYYY-MM-DD"
        public ?string $experiencia = null,
        public ?string $coche = null,           // Puede ser "Si/No" o booleano
        
        // Estos son los datos añadidos (tags)
        public array $idiomas = [],
        public array $habilidades = [],
        public array $intereses = [],
        public array $disponibilidad = [] // Ej: ["Lunes", "De 7 a 8"]
    ) {}
}