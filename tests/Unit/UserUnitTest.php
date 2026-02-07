<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{

    // TC01 Valid: role = 'student', true
    public function test_TC01_isStudent_returns_true_when_role_is_student()
    {
        $user = new User(['role' => 'student']);
        $this->assertTrue($user->isStudent());
    }

    // TC02 Invalid: role = 'admin', false
    public function test_TC02_isStudent_returns_false_when_role_is_admin()
    {
        $user = new User(['role' => 'admin']);
        $this->assertFalse($user->isStudent());
    }

    // TC03 Invalid: role = 'superadmin', false
    public function test_TC03_isStudent_returns_false_when_role_is_superadmin()
    {
        $user = new User(['role' => 'superadmin']);
        $this->assertFalse($user->isStudent());
    }

    // TC04 Boundary: role = null, false
    public function test_TC04_isStudent_returns_false_when_role_is_null()
    {
        $user = new User(['role' => null]);
        $this->assertFalse($user->isStudent());
    }

    // TC05 Boundary: role = '' (empty), false
    public function test_TC05_isStudent_returns_false_when_role_is_empty()
    {
        $user = new User(['role' => '']);
        $this->assertFalse($user->isStudent());
    }

    // TC06 Edge Case: role = 'STUDENT' (uppercase), false
    public function test_TC06_isStudent_returns_false_when_role_is_uppercase()
    {
        $user = new User(['role' => 'STUDENT']);
        $this->assertFalse($user->isStudent());
    }


    /** =====================================================
     * ADMIN ROLE TESTS (isAdmin)
     * ===================================================== */

    // TC07 Valid: role = 'admin', true
    public function test_TC07_isAdmin_returns_true_when_role_is_admin()
    {
        $user = new User(['role' => 'admin']);
        $this->assertTrue($user->isAdmin());
    }

    // TC08 Invalid: role = 'student', false
    public function test_TC08_isAdmin_returns_false_when_role_is_student()
    {
        $user = new User(['role' => 'student']);
        $this->assertFalse($user->isAdmin());
    }

    // TC09 Invalid: role = 'superadmin', false
    public function test_TC09_isAdmin_returns_false_when_role_is_superadmin()
    {
        $user = new User(['role' => 'superadmin']);
        $this->assertFalse($user->isAdmin());
    }

    // TC10 Invalid: role = 'guest', false
    public function test_TC10_isAdmin_returns_false_when_role_is_invalid()
    {
        $user = new User(['role' => 'guest']);
        $this->assertFalse($user->isAdmin());
    }

    // TC11 Boundary: role = null, false
    public function test_TC11_isAdmin_returns_false_when_role_is_null()
    {
        $user = new User(['role' => null]);
        $this->assertFalse($user->isAdmin());
    }

    // TC12 Edge Case: role = 'ADMIN' (uppercase), false
    public function test_TC12_isAdmin_returns_false_when_role_is_uppercase()
    {
        $user = new User(['role' => 'ADMIN']);
        $this->assertFalse($user->isAdmin());
    }
    
    // TC13 Valid: role = 'superadmin', true
    public function test_TC13_isSuperAdmin_returns_true_when_role_is_superadmin()
    {
        $user = new User(['role' => 'superadmin']);
        $this->assertTrue($user->isSuperAdmin());
    }

    // TC14 Invalid: role = 'admin', false
    public function test_TC14_isSuperAdmin_returns_false_when_role_is_admin()
    {
        $user = new User(['role' => 'admin']);
        $this->assertFalse($user->isSuperAdmin());
    }

    // TC15 Invalid: role = 'student', false
    public function test_TC15_isSuperAdmin_returns_false_when_role_is_student()
    {
        $user = new User(['role' => 'student']);
        $this->assertFalse($user->isSuperAdmin());
    }

    // TC16 Boundary: role = '' (empty), false
    public function test_TC16_isSuperAdmin_returns_false_when_role_is_empty()
    {
        $user = new User(['role' => '']);
        $this->assertFalse($user->isSuperAdmin());
    }

    // TC17 Edge Case: role = 'SUPERADMIN' (uppercase), false
    public function test_TC17_isSuperAdmin_returns_false_when_role_is_uppercase()
    {
        $user = new User(['role' => 'SUPERADMIN']);
        $this->assertFalse($user->isSuperAdmin());
    }

    // TC18 Edge Case: role = ' superadmin ' (whitespace), false
    public function test_TC18_isSuperAdmin_returns_false_when_role_has_whitespace()
    {
        $user = new User(['role' => ' superadmin ']);
        $this->assertFalse($user->isSuperAdmin());
    }
}
