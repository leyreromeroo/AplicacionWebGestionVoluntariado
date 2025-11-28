<?php

namespace App\Controller;

use App\Entity\Voluntario;
use App\Model\RegistroVoluntarioDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Organizacion;
use App\Model\RegistroOrganizacionDTO;


#[Route('/api/auth', name: 'api_auth_')]
class RegistrationController extends AbstractController
{
    #[Route('/register/voluntario', name: 'register_voluntario', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $json = $request->getContent();

        // 1. Deserializar JSON a DTO
        try {
            /** @var RegistroVoluntarioDTO $dto */
            $dto = $serializer->deserialize($json, RegistroVoluntarioDTO::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Datos inválidos'], 400);
        }

        // 2. Comprobar duplicados
        $repo = $entityManager->getRepository(Voluntario::class);
        if ($repo->findOneBy(['dni' => $dto->dni])) {
            return $this->json(['error' => 'El DNI ya existe'], 409);
        }
        if ($repo->findOneBy(['correo' => $dto->correo])) {
            return $this->json(['error' => 'El correo ya existe'], 409);
        }

        // 3. Mapear DTO a Entidad
        $voluntario = new Voluntario();
        $voluntario->setDni($dto->dni);
        $voluntario->setCorreo($dto->correo);
        $voluntario->setZona($dto->zona);
        $voluntario->setCursoCiclos($dto->ciclo);
        $voluntario->setExperiencia($dto->experiencia);

        // Lógica Nombre Completo -> Nombre + Apellidos
        $partes = explode(' ', trim($dto->nombreCompleto));
        $voluntario->setNombre($partes[0] ?? '');
        $voluntario->setApellido1($partes[1] ?? '');
        if (count($partes) > 2) {
            $voluntario->setApellido2(implode(' ', array_slice($partes, 2)));
        }

        // Lógica Fecha
        if ($dto->fechaNacimiento) {
            try {
                $voluntario->setFechaNacimiento(new \DateTime($dto->fechaNacimiento));
            } catch (\Exception $e) {}
        }

        // Lógica Coche (Normalizar "Si"/"No"/true)
        $cocheStr = strtolower((string)$dto->coche);
        $voluntario->setCoche(in_array($cocheStr, ['si', 'yes', 'true', '1']));

        // Lógica Arrays -> String
        $voluntario->setIdiomas(implode(', ', $dto->idiomas));
        $voluntario->setHabilidades(implode(', ', $dto->habilidades));
        $voluntario->setIntereses(implode(', ', $dto->intereses));

        // 4. Encriptar y Guardar
        $hashedPassword = $passwordHasher->hashPassword($voluntario, $dto->password);
        $voluntario->setPassword($hashedPassword);

        $entityManager->persist($voluntario);
        $entityManager->flush();

        return $this->json(['message' => 'Voluntario registrado'], 201);
    }
    public function registerOrganizacion(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $json = $request->getContent();

        try {
            /** @var RegistroOrganizacionDTO $dto */
            $dto = $serializer->deserialize($json, RegistroOrganizacionDTO::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Datos inválidos'], 400);
        }

        // Validar si existe CIF o Email
        $repo = $entityManager->getRepository(Organizacion::class);
        if ($repo->findOneBy(['cif' => $dto->cif])) return $this->json(['error' => 'CIF ya registrado'], 409);
        if ($repo->findOneBy(['email' => $dto->email])) return $this->json(['error' => 'Email ya registrado'], 409);

        // Crear Entidad
        $org = new Organizacion();
        $org->setCif($dto->cif);
        $org->setNombre($dto->nombre);
        $org->setEmail($dto->email);
        $org->setSector($dto->sector);
        $org->setLocalidad($dto->zona); // Mapeamos zona -> localidad
        $org->setDescripcion($dto->descripcion);

        // Password
        $org->setPassword($passwordHasher->hashPassword($org, $dto->password));

        $entityManager->persist($org);
        $entityManager->flush();

        return $this->json(['message' => 'Organización creada'], 201);
    }
}