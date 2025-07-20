<?php

namespace Tests\Unit;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_ser_creado()
    {
        echo "🔧 Configurando roles de prueba...\n";
        
        // Crear rol cliente
        $rolCliente = Role::create([
            'nombre' => 'cliente',
            'is_superadmin' => false
        ]);
        echo "   👤 Rol 'cliente' creado\n";

        echo "🧪 Creando usuario...\n";
        
        // Crear usuario
        $usuario = User::create([
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan.perez@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Verificaciones
        $this->assertNotNull($usuario);
        $this->assertEquals('Juan', $usuario->first_name);
        $this->assertEquals('Pérez', $usuario->last_name);
        $this->assertEquals('juan.perez@test.com', $usuario->email);
        $this->assertTrue(Hash::check('password123', $usuario->password));
        
        // Verificar que se guardó en la base de datos
        $this->assertDatabaseHas('usuarios', [
            'email' => 'juan.perez@test.com',
            'first_name' => 'Juan'
        ]);

        echo "✅ Usuario '{$usuario->first_name} {$usuario->last_name}' creado exitosamente con ID: {$usuario->id}\n";
    }

    public function test_usuario_puede_tener_roles()
    {
        echo "🔧 Configurando datos de prueba...\n";
        
        // Crear roles
        $rolCliente = Role::create([
            'nombre' => 'cliente',
            'is_superadmin' => false
        ]);
        
        $rolAdmin = Role::create([
            'nombre' => 'admin', 
            'is_superadmin' => true
        ]);
        echo "   🎭 Roles 'cliente' y 'admin' creados\n";

        // Crear usuario
        $usuario = User::create([
            'first_name' => 'María',
            'last_name' => 'González',
            'email' => 'maria.gonzalez@test.com',
            'password' => Hash::make('password123'),
        ]);
        echo "   👤 Usuario María González creado\n";

        echo "🔗 Asignando roles...\n";
        
        // Asignar rol de cliente
        $usuario->roles()->attach($rolCliente->id);
        
        // Verificar relación
        $this->assertTrue($usuario->roles->contains($rolCliente));
        $this->assertEquals('cliente', $usuario->roles->first()->nombre);
        $this->assertFalse($usuario->roles->first()->is_superadmin);
        
        echo "   ✅ Rol 'cliente' asignado correctamente\n";

        // Cambiar a admin
        $usuario->roles()->detach($rolCliente->id);
        $usuario->roles()->attach($rolAdmin->id);
        $usuario->refresh(); // Refrescar las relaciones
        
        $this->assertTrue($usuario->roles->contains($rolAdmin));
        $this->assertEquals('admin', $usuario->roles->first()->nombre);
        $this->assertTrue($usuario->roles->first()->is_superadmin);
        
        echo "   ✅ Rol cambiado a 'admin' exitosamente\n";
        echo "🎉 Test completado: Usuario puede tener y cambiar roles\n";
    }

    public function test_diferentes_tipos_de_usuarios()
    {
        echo "🔧 Configurando sistema de roles...\n";
        
        // Crear roles
        $rolCliente = Role::create([
            'nombre' => 'cliente',
            'is_superadmin' => false
        ]);
        
        $rolAdmin = Role::create([
            'nombre' => 'admin',
            'is_superadmin' => true
        ]);
        echo "   🎭 Roles configurados\n";

        echo "🧪 Creando diferentes tipos de usuarios...\n";
        
        // Crear usuario cliente
        $cliente = User::create([
            'first_name' => 'Carlos',
            'last_name' => 'Cliente',
            'email' => 'carlos.cliente@test.com',
            'password' => Hash::make('password123'),
        ]);
        $cliente->roles()->attach($rolCliente->id);
        
        // Crear usuario admin
        $admin = User::create([
            'first_name' => 'Ana',
            'last_name' => 'Admin',
            'email' => 'ana.admin@test.com',
            'password' => Hash::make('admin123'),
        ]);
        $admin->roles()->attach($rolAdmin->id);

        echo "   👤 Cliente: {$cliente->first_name} {$cliente->last_name}\n";
        echo "   👑 Admin: {$admin->first_name} {$admin->last_name}\n";

        // Verificar tipos
        $this->assertEquals('cliente', $cliente->roles->first()->nombre);
        $this->assertFalse($cliente->roles->first()->is_superadmin);
        
        $this->assertEquals('admin', $admin->roles->first()->nombre);
        $this->assertTrue($admin->roles->first()->is_superadmin);

        // Verificar que ambos existen en la base de datos
        $this->assertDatabaseHas('usuarios', ['email' => 'carlos.cliente@test.com']);
        $this->assertDatabaseHas('usuarios', ['email' => 'ana.admin@test.com']);
        
        // Verificar relaciones en la tabla pivot
        $this->assertDatabaseHas('usuarios_roles', [
            'id_usuario' => $cliente->id,
            'id_rol' => $rolCliente->id
        ]);
        
        $this->assertDatabaseHas('usuarios_roles', [
            'id_usuario' => $admin->id,
            'id_rol' => $rolAdmin->id
        ]);

        echo "✅ Sistema de roles funcionando: Cliente (normal) y Admin (superadmin) creados exitosamente\n";
    }
}
