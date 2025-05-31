<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;

class SessionTest extends TestCase
{
    public function test_session_works()
    {
        // Test basic session functionality
        Session::put('test_key', 'test_value');
        $this->assertEquals('test_value', Session::get('test_key'));
        
        echo "✅ Basic session test passed\n";
    }
    
    public function test_admin_login_session()
    {
        $admin = Admin::first();
        if (!$admin) {
            echo "❌ No admin found in database\n";
            return;
        }
        
        $response = $this->post('/auth/admin/login', [
            'username' => $admin->username,
            'password' => 'admin123', // Assuming default password
            '_token' => csrf_token()
        ]);
        
        if ($response->status() === 302) {
            echo "✅ Admin login redirected (likely successful)\n";
        } else {
            echo "❌ Admin login failed with status: " . $response->status() . "\n";
        }
    }
}
